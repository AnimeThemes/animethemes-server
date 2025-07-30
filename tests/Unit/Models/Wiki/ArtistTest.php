<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistImage;
use App\Pivots\Wiki\ArtistMember;
use App\Pivots\Wiki\ArtistResource;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('searchable as', function () {
    $artist = Artist::factory()->createOne();

    static::assertIsString($artist->searchableAs());
});

test('to searchable array', function () {
    $artist = Artist::factory()->createOne();

    static::assertIsArray($artist->toSearchableArray());
});

test('nameable', function () {
    $artist = Artist::factory()->createOne();

    static::assertIsString($artist->getName());
});

test('has subtitle', function () {
    $artist = Artist::factory()->createOne();

    static::assertIsString($artist->getSubtitle());
});

test('songs', function () {
    $songCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(Song::factory()->count($songCount))
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $artist->songs());
    static::assertEquals($songCount, $artist->songs()->count());
    static::assertInstanceOf(Song::class, $artist->songs()->first());
    static::assertEquals(ArtistSong::class, $artist->songs()->getPivotClass());
});

test('external resources', function () {
    $resourceCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(ExternalResource::factory()->count($resourceCount), 'resources')
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $artist->resources());
    static::assertEquals($resourceCount, $artist->resources()->count());
    static::assertInstanceOf(ExternalResource::class, $artist->resources()->first());
    static::assertEquals(ArtistResource::class, $artist->resources()->getPivotClass());
});

test('members', function () {
    $memberCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(Artist::factory()->count($memberCount), 'members')
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $artist->members());
    static::assertEquals($memberCount, $artist->members()->count());
    static::assertInstanceOf(Artist::class, $artist->members()->first());
    static::assertEquals(ArtistMember::class, $artist->members()->getPivotClass());
});

test('groups', function () {
    $groupCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(Artist::factory()->count($groupCount), 'groups')
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $artist->groups());
    static::assertEquals($groupCount, $artist->groups()->count());
    static::assertInstanceOf(Artist::class, $artist->groups()->first());
    static::assertEquals(ArtistMember::class, $artist->groups()->getPivotClass());
});

test('images', function () {
    $imageCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(Image::factory()->count($imageCount))
        ->createOne();

    static::assertInstanceOf(BelongsToMany::class, $artist->images());
    static::assertEquals($imageCount, $artist->images()->count());
    static::assertInstanceOf(Image::class, $artist->images()->first());
    static::assertEquals(ArtistImage::class, $artist->images()->getPivotClass());
});
