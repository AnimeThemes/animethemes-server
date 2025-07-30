<?php

declare(strict_types=1);

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Image;
use App\Pivots\List\PlaylistImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('casts season to enum', function () {
    $playlist = Playlist::factory()->createOne();

    $visibility = $playlist->visibility;

    $this->assertInstanceOf(PlaylistVisibility::class, $visibility);
});

test('nameable', function () {
    $playlist = Playlist::factory()->createOne();

    $this->assertIsString($playlist->getName());
});

test('has subtitle', function () {
    $playlist = Playlist::factory()
        ->for(User::factory())
        ->createOne();

    $this->assertIsString($playlist->getSubtitle());
});

test('searchable if public', function () {
    $playlist = Playlist::factory()
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    $this->assertTrue($playlist->shouldBeSearchable());
});

test('not searchable if not public', function () {
    $visibility = null;

    while ($visibility == null) {
        $candidate = Arr::random(PlaylistVisibility::cases());
        if ($candidate !== PlaylistVisibility::PUBLIC) {
            $visibility = $candidate;
        }
    }

    $playlist = Playlist::factory()
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => $visibility->value,
        ]);

    $this->assertFalse($playlist->shouldBeSearchable());
});

test('hashids nullable user', function () {
    $playlist = Playlist::factory()->createOne();

    $this->assertEmpty(array_diff([$playlist->playlist_id], $playlist->hashids()));
    $this->assertEmpty(array_diff($playlist->hashids(), [$playlist->playlist_id]));
});

test('hashids non null user', function () {
    $user = User::factory()->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    $this->assertEmpty(array_diff([$user->id, $playlist->playlist_id], $playlist->hashids()));
    $this->assertEmpty(array_diff($playlist->hashids(), [$user->id, $playlist->playlist_id]));
});

test('user', function () {
    $playlist = Playlist::factory()
        ->for(User::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $playlist->user());
    $this->assertInstanceOf(User::class, $playlist->user()->first());
});

test('first', function () {
    $playlist = Playlist::factory()
        ->createOne();

    $first = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $playlist->first()->associate($first)->save();

    $this->assertInstanceOf(BelongsTo::class, $playlist->first());
    $this->assertInstanceOf(PlaylistTrack::class, $playlist->first()->first());
});

test('last', function () {
    $playlist = Playlist::factory()->createOne();

    $last = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $playlist->last()->associate($last)->save();

    $this->assertInstanceOf(BelongsTo::class, $playlist->last());
    $this->assertInstanceOf(PlaylistTrack::class, $playlist->last()->first());
});

test('images', function () {
    $imageCount = fake()->randomDigitNotNull();

    $playlist = Playlist::factory()
        ->has(Image::factory()->count($imageCount))
        ->createOne();

    $this->assertInstanceOf(BelongsToMany::class, $playlist->images());
    $this->assertEquals($imageCount, $playlist->images()->count());
    $this->assertInstanceOf(Image::class, $playlist->images()->first());
    $this->assertEquals(PlaylistImage::class, $playlist->images()->getPivotClass());
});

test('tracks', function () {
    $trackCount = fake()->randomDigitNotNull();

    $playlist = Playlist::factory()->createOne();

    PlaylistTrack::factory()
        ->for($playlist)
        ->count($trackCount)
        ->create();

    $this->assertInstanceOf(HasMany::class, $playlist->tracks());
    $this->assertEquals($trackCount, $playlist->tracks()->count());
    $this->assertInstanceOf(PlaylistTrack::class, $playlist->tracks()->first());
});
