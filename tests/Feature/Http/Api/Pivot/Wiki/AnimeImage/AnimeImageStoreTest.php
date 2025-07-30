<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\AnimeImage;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $anime = Anime::factory()->createOne();
    $image = Image::factory()->createOne();

    $response = post(route('api.animeimage.store', ['anime' => $anime, 'image' => $image]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $anime = Anime::factory()->createOne();
    $image = Image::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animeimage.store', ['anime' => $anime, 'image' => $image]));

    $response->assertForbidden();
});

test('create', function () {
    $anime = Anime::factory()->createOne();
    $image = Image::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(Anime::class),
            CrudPermission::CREATE->format(Image::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animeimage.store', ['anime' => $anime, 'image' => $image]));

    $response->assertCreated();
    $this->assertDatabaseCount(AnimeImage::class, 1);
});
