<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\ArtistImage;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $artistImage = ArtistImage::factory()
        ->for(Artist::factory())
        ->for(Image::factory())
        ->createOne();

    $response = $this->delete(route('api.artistimage.destroy', ['artist' => $artistImage->artist, 'image' => $artistImage->image]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artistImage = ArtistImage::factory()
        ->for(Artist::factory())
        ->for(Image::factory())
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.artistimage.destroy', ['artist' => $artistImage->artist, 'image' => $artistImage->image]));

    $response->assertForbidden();
});

test('not found', function () {
    $artist = Artist::factory()->createOne();
    $image = Image::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Artist::class),
            CrudPermission::DELETE->format(Image::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.artistimage.destroy', ['artist' => $artist, 'image' => $image]));

    $response->assertNotFound();
});

test('deleted', function () {
    $artistImage = ArtistImage::factory()
        ->for(Artist::factory())
        ->for(Image::factory())
        ->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Artist::class),
            CrudPermission::DELETE->format(Image::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.artistimage.destroy', ['artist' => $artistImage->artist, 'image' => $artistImage->image]));

    $response->assertOk();
    static::assertModelMissing($artistImage);
});
