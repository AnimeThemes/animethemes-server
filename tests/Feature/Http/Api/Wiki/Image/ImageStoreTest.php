<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\Field\Wiki\Image\ImageFileField;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
    $image = Image::factory()->makeOne();

    $response = post(route('api.image.store', $image->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $image = Image::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.image.store', $image->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Image::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.image.store'));

    $response->assertJsonValidationErrors([
        ImageFileField::ATTRIBUTE_FILE,
    ]);
});

test('create', function () {
    $fs = Storage::fake(Config::get('image.disk'));

    $facet = Arr::random(ImageFacet::cases());

    $parameters = [Image::ATTRIBUTE_FACET => $facet->localize()];

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Image::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.image.store', $parameters), [
        ImageFileField::ATTRIBUTE_FILE => UploadedFile::fake()->image(fake()->word().'.jpg'),
    ]);

    $response->assertCreated();
    $this->assertCount(1, $fs->allFiles());
    $this->assertDatabaseCount(Image::class, 1);
});
