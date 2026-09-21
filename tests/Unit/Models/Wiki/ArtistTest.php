<?php

declare(strict_types=1);

use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Performance;
use App\Models\Wiki\Song;
use App\Models\Wiki\Synonym;
use App\Pivots\Morph\Imageable;
use App\Pivots\Morph\Resourceable;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('searchable as', function (): void {
    $artist = Artist::factory()->createOne();

    expect($artist->searchableAs())->toBeString();
});

test('to searchable array', function (): void {
    $artist = Artist::factory()->createOne();

    expect($artist->toSearchableArray())->toBeArray();
});

test('nameable', function (): void {
    $artist = Artist::factory()->createOne();

    expect($artist->getName())->toBeString();
});

test('has subtitle', function (): void {
    $artist = Artist::factory()->createOne();

    expect($artist->getSubtitle())->toBeString();
});

test('synonyms', function (): void {
    $synonymCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(Synonym::factory()->count($synonymCount))
        ->createOne();

    expect($artist->synonyms())->toBeInstanceOf(MorphMany::class);
    expect($artist->synonyms()->count())->toEqual($synonymCount);
    expect($artist->synonyms()->first())->toBeInstanceOf(Synonym::class);
});

test('songs', function (): void {
    $songCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(Song::factory()->count($songCount))
        ->createOne();

    expect($artist->songs())->toBeInstanceOf(BelongsToMany::class);
    expect($artist->songs()->count())->toEqual($songCount);
    expect($artist->songs()->first())->toBeInstanceOf(Song::class);
});

test('performances', function (): void {
    $performanceCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->createOne();

    Performance::factory()
        ->for($artist, Performance::RELATION_ARTIST)
        ->count($performanceCount)
        ->create();

    expect($artist->performances())->toBeInstanceOf(HasMany::class);
    expect($artist->performances()->count())->toEqual($performanceCount);
    expect($artist->performances()->first())->toBeInstanceOf(Performance::class);
});

test('member performances', function (): void {
    $performanceCount = fake()->randomDigitNotNull();

    $member = Artist::factory()
        ->createOne();

    Performance::factory()
        ->for(Artist::factory(), Performance::RELATION_ARTIST)
        ->for($member, Performance::RELATION_MEMBER)
        ->count($performanceCount)
        ->create();

    expect($member->memberPerformances())->toBeInstanceOf(HasMany::class);
    expect($member->memberPerformances()->count())->toEqual($performanceCount);
    expect($member->memberPerformances()->first())->toBeInstanceOf(Performance::class);
});

test('members', function (): void {
    $memberCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(Artist::factory()->count($memberCount), Artist::RELATION_MEMBERS)
        ->createOne();

    expect($artist->members())->toBeInstanceOf(BelongsToMany::class);
    expect($artist->members()->count())->toEqual($memberCount);
    expect($artist->members()->first())->toBeInstanceOf(Artist::class);
    expect($artist->members()->getPivotClass())->toEqual(ArtistMember::class);
});

test('groups', function (): void {
    $groupCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(Artist::factory()->count($groupCount), Artist::RELATION_GROUPS)
        ->createOne();

    expect($artist->groups())->toBeInstanceOf(BelongsToMany::class);
    expect($artist->groups()->count())->toEqual($groupCount);
    expect($artist->groups()->first())->toBeInstanceOf(Artist::class);
    expect($artist->groups()->getPivotClass())->toEqual(ArtistMember::class);
});

test('images', function (): void {
    $imageCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(Image::factory()->count($imageCount))
        ->createOne();

    expect($artist->images())->toBeInstanceOf(MorphToMany::class);
    expect($artist->images()->count())->toEqual($imageCount);
    expect($artist->images()->first())->toBeInstanceOf(Image::class);
    expect($artist->images()->getPivotClass())->toEqual(Imageable::class);
});

test('external resources', function (): void {
    $resourceCount = fake()->randomDigitNotNull();

    $artist = Artist::factory()
        ->has(ExternalResource::factory()->count($resourceCount), 'resources')
        ->createOne();

    expect($artist->resources())->toBeInstanceOf(MorphToMany::class);
    expect($artist->resources()->count())->toEqual($resourceCount);
    expect($artist->resources()->first())->toBeInstanceOf(ExternalResource::class);
    expect($artist->resources()->getPivotClass())->toEqual(Resourceable::class);
});
