<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\AnimeImage;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $animeImage = AnimeImage::factory()
        ->for(Anime::factory())
        ->for(Image::factory())
        ->createOne();

    $response = delete(route('api.animeimage.destroy', ['anime' => $animeImage->anime, 'image' => $animeImage->image]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $animeImage = AnimeImage::factory()
        ->for(Anime::factory())
        ->for(Image::factory())
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animeimage.destroy', ['anime' => $animeImage->anime, 'image' => $animeImage->image]));

    $response->assertForbidden();
});

test('not found', function () {
    $anime = Anime::factory()->createOne();
    $image = Image::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Anime::class),
            CrudPermission::DELETE->format(Image::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animeimage.destroy', ['anime' => $anime, 'image' => $image]));

    $response->assertNotFound();
});

test('deleted', function () {
    $animeImage = AnimeImage::factory()
        ->for(Anime::factory())
        ->for(Image::factory())
        ->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Anime::class),
            CrudPermission::DELETE->format(Image::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animeimage.destroy', ['anime' => $animeImage->anime, 'image' => $animeImage->image]));

    $response->assertOk();
    $this->assertModelMissing($animeImage);
});
