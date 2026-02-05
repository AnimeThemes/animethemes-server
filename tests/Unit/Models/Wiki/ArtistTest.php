<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use App\Pivots\Morph\Imageable;
use App\Pivots\Morph\Resourceable;
use App\Pivots\Wiki\ArtistMember;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('searchable as', function () {
    $artist = Artist::factory()->createOne();

    $this->assertIsString($artist->searchableAs());
});

test('to searchable array', function () {
    $artist = Artist::factory()->createOne();

    $this->assertIsArray($artist->toSearchableArray());
});

test('nameable', function () {
    $artist = Artist::factory()->createOne();

    $this->assertIsString($artist->getName());
});

test('has subtitle', function () {
    $artist = Artist::factory()->createOne();

    $this->assertIsString($artist->getSubtitle());
});

test('songs', function () {
    $songCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(Song::factory()->count($songCount))
        ->createOne();

    $this->assertInstanceOf(BelongsToMany::class, $artist->songs());
    $this->assertEquals($songCount, $artist->songs()->count());
    $this->assertInstanceOf(Song::class, $artist->songs()->first());
    $this->assertEquals(ArtistSong::class, $artist->songs()->getPivotClass());
});

test('performances', function () {
    $performanceCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->createOne();

    Performance::factory()
        ->artist($artist)
        ->count($performanceCount)
        ->create();

    $this->assertInstanceOf(MorphMany::class, $artist->performances());
    $this->assertEquals($performanceCount, $artist->performances()->count());
    $this->assertInstanceOf(Performance::class, $artist->performances()->first());
});

test('memberships', function () {
    $membershipCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(Membership::factory()->count($membershipCount), Artist::RELATION_MEMBERSHIPS)
        ->createOne();

    $this->assertInstanceOf(HasMany::class, $artist->memberships());
    $this->assertEquals($membershipCount, $artist->memberships()->count());
    $this->assertInstanceOf(Membership::class, $artist->memberships()->first());
});

test('groupships', function () {
    $groupshipCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(Membership::factory()->count($groupshipCount), Artist::RELATION_GROUPSHIPS)
        ->createOne();

    $this->assertInstanceOf(HasMany::class, $artist->groupships());
    $this->assertEquals($groupshipCount, $artist->groupships()->count());
    $this->assertInstanceOf(Membership::class, $artist->groupships()->first());
});

test('members', function () {
    $memberCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(Artist::factory()->count($memberCount), Artist::RELATION_MEMBERS)
        ->createOne();

    $this->assertInstanceOf(BelongsToMany::class, $artist->members());
    $this->assertEquals($memberCount, $artist->members()->count());
    $this->assertInstanceOf(Artist::class, $artist->members()->first());
    $this->assertEquals(ArtistMember::class, $artist->members()->getPivotClass());
});

test('groups', function () {
    $groupCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(Artist::factory()->count($groupCount), Artist::RELATION_GROUPS)
        ->createOne();

    $this->assertInstanceOf(BelongsToMany::class, $artist->groups());
    $this->assertEquals($groupCount, $artist->groups()->count());
    $this->assertInstanceOf(Artist::class, $artist->groups()->first());
    $this->assertEquals(ArtistMember::class, $artist->groups()->getPivotClass());
});

test('images', function () {
    $imageCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(Image::factory()->count($imageCount))
        ->createOne();

    $this->assertInstanceOf(MorphToMany::class, $artist->images());
    $this->assertEquals($imageCount, $artist->images()->count());
    $this->assertInstanceOf(Image::class, $artist->images()->first());
    $this->assertEquals(Imageable::class, $artist->images()->getPivotClass());
});

test('external resources', function () {
    $resourceCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(ExternalResource::factory()->count($resourceCount), 'resources')
        ->createOne();

    $this->assertInstanceOf(MorphToMany::class, $artist->resources());
    $this->assertEquals($resourceCount, $artist->resources()->count());
    $this->assertInstanceOf(ExternalResource::class, $artist->resources()->first());
    $this->assertEquals(Resourceable::class, $artist->resources()->getPivotClass());
});
