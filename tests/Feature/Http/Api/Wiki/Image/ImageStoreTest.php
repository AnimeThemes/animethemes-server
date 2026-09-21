<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\Field\Wiki\Image\ImageFileField;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

pest()->use(WithFaker::class);

test('protected', function (): void {
    $image = Image::factory()->makeOne();

    $response = post(route('api.image.store', $image->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $image = Image::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.image.store', $image->toArray()));

    $response->assertForbidden();
});

test('required fields', function (): void {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Image::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.image.store'));

    $response->assertJsonValidationErrors([
        ImageFileField::ATTRIBUTE_FILE,
    ]);
});

test('create', function (): void {
    $fs = Storage::fake(Config::get('image.disk'));

    $facet = Arr::random(ImageFacet::cases());

    $parameters = [Image::ATTRIBUTE_FACET => $facet->localize()];

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Image::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.image.store', $parameters), [
        ImageFileField::ATTRIBUTE_FILE => UploadedFile::fake()->image(fake()->word().'.jpg'),
    ]);

    $response->assertCreated();
    expect($fs->allFiles())->toHaveCount(1);
    $this->assertDatabaseCount(Image::class, 1);
});
