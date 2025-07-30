<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\ArtistImage;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $artist = Artist::factory()->createOne();
    $image = Image::factory()->createOne();

    $response = post(route('api.artistimage.store', ['artist' => $artist, 'image' => $image]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artist = Artist::factory()->createOne();
    $image = Image::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.artistimage.store', ['artist' => $artist, 'image' => $image]));

    $response->assertForbidden();
});

test('create', function () {
    $artist = Artist::factory()->createOne();
    $image = Image::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(Artist::class),
            CrudPermission::CREATE->format(Image::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.artistimage.store', ['artist' => $artist, 'image' => $image]));

    $response->assertCreated();
    $this->assertDatabaseCount(ArtistImage::class, 1);
});
