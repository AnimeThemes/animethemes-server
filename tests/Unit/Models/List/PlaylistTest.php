<?php

declare(strict_types=1);

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Image;
use App\Pivots\Morph\Imageable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

pest()->use(WithFaker::class);

test('casts season to enum', function (): void {
    $playlist = Playlist::factory()->createOne();

    $visibility = $playlist->visibility;

    expect($visibility)->toBeInstanceOf(PlaylistVisibility::class);
});

test('nameable', function (): void {
    $playlist = Playlist::factory()->createOne();

    expect($playlist->getName())->toBeString();
});

test('has subtitle', function (): void {
    $playlist = Playlist::factory()
        ->for(User::factory())
        ->createOne();

    expect($playlist->getSubtitle())->toBeString();
});

test('searchable if public', function (): void {
    $playlist = Playlist::factory()
        ->createOne([
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC->value,
        ]);

    expect($playlist->shouldBeSearchable())->toBeTrue();
});

test('not searchable if not public', function (): void {
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

    expect($playlist->shouldBeSearchable())->toBeFalse();
});

test('hashids nullable user', function (): void {
    $playlist = Playlist::factory()->createOne();

    expect(array_diff([$playlist->playlist_id], $playlist->hashids()))->toBeEmpty();
    expect(array_diff($playlist->hashids(), [$playlist->playlist_id]))->toBeEmpty();
});

test('hashids non null user', function (): void {
    $user = User::factory()->createOne();

    $playlist = Playlist::factory()
        ->for($user)
        ->createOne();

    expect(array_diff([$user->id, $playlist->playlist_id], $playlist->hashids()))->toBeEmpty();
    expect(array_diff($playlist->hashids(), [$user->id, $playlist->playlist_id]))->toBeEmpty();
});

test('user', function (): void {
    $playlist = Playlist::factory()
        ->for(User::factory())
        ->createOne();

    expect($playlist->user())->toBeInstanceOf(BelongsTo::class);
    expect($playlist->user()->first())->toBeInstanceOf(User::class);
});

test('first', function (): void {
    $playlist = Playlist::factory()
        ->createOne();

    $first = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $playlist->first()->associate($first)->save();

    expect($playlist->first())->toBeInstanceOf(BelongsTo::class);
    expect($playlist->first()->first())->toBeInstanceOf(PlaylistTrack::class);
});

test('last', function (): void {
    $playlist = Playlist::factory()->createOne();

    $last = PlaylistTrack::factory()
        ->for($playlist)
        ->createOne();

    $playlist->last()->associate($last)->save();

    expect($playlist->last())->toBeInstanceOf(BelongsTo::class);
    expect($playlist->last()->first())->toBeInstanceOf(PlaylistTrack::class);
});

test('images', function (): void {
    $imageCount = fake()->randomDigitNotNull();

    $playlist = Playlist::factory()
        ->has(Image::factory()->count($imageCount))
        ->createOne();

    expect($playlist->images())->toBeInstanceOf(MorphToMany::class);
    expect($playlist->images()->count())->toEqual($imageCount);
    expect($playlist->images()->first())->toBeInstanceOf(Image::class);
    expect($playlist->images()->getPivotClass())->toEqual(Imageable::class);
});

test('tracks', function (): void {
    $trackCount = fake()->randomDigitNotNull();

    $playlist = Playlist::factory()->createOne();

    PlaylistTrack::factory()
        ->for($playlist)
        ->count($trackCount)
        ->create();

    expect($playlist->tracks())->toBeInstanceOf(HasMany::class);
    expect($playlist->tracks()->count())->toEqual($trackCount);
    expect($playlist->tracks()->first())->toBeInstanceOf(PlaylistTrack::class);
});
