<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ImageFacet;
use App\Events\Wiki\Image\ImageForceDeleting;
use App\Models\List\Playlist;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\Morph\Imageable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

pest()->use(WithFaker::class);

test('casts facet to enum', function (): void {
    $image = Image::factory()->createOne();

    $facet = $image->facet;

    expect($facet)->toBeInstanceOf(ImageFacet::class);
});

test('nameable', function (): void {
    $image = Image::factory()->createOne();

    expect($image->getName())->toBeString();
});

test('has subtitle', function (): void {
    $image = Image::factory()->createOne();

    expect($image->getSubtitle())->toBeString();
});

test('anime', function (): void {
    $animeCount = fake()->randomDigitNotNull();

    $image = Image::factory()
        ->has(Anime::factory()->count($animeCount))
        ->createOne();

    expect($image->anime())->toBeInstanceOf(MorphToMany::class);
    expect($image->anime()->count())->toEqual($animeCount);
    expect($image->anime()->first())->toBeInstanceOf(Anime::class);
    expect($image->anime()->getPivotClass())->toEqual(Imageable::class);
});

test('artists', function (): void {
    $artistCount = fake()->randomDigitNotNull();

    $image = Image::factory()
        ->has(Artist::factory()->count($artistCount))
        ->createOne();

    expect($image->artists())->toBeInstanceOf(MorphToMany::class);
    expect($image->artists()->count())->toEqual($artistCount);
    expect($image->artists()->first())->toBeInstanceOf(Artist::class);
    expect($image->artists()->getPivotClass())->toEqual(Imageable::class);
});

test('studios', function (): void {
    $studioCount = fake()->randomDigitNotNull();

    $image = Image::factory()
        ->has(Studio::factory()->count($studioCount))
        ->createOne();

    expect($image->studios())->toBeInstanceOf(MorphToMany::class);
    expect($image->studios()->count())->toEqual($studioCount);
    expect($image->studios()->first())->toBeInstanceOf(Studio::class);
    expect($image->studios()->getPivotClass())->toEqual(Imageable::class);
});

test('playlists', function (): void {
    $playlistCount = fake()->randomDigitNotNull();

    $image = Image::factory()
        ->has(Playlist::factory()->count($playlistCount))
        ->createOne();

    expect($image->playlists())->toBeInstanceOf(MorphToMany::class);
    expect($image->playlists()->count())->toEqual($playlistCount);
    expect($image->playlists()->first())->toBeInstanceOf(Playlist::class);
    expect($image->playlists()->getPivotClass())->toEqual(Imageable::class);
});

test('image storage deletion', function (): void {
    $fs = Storage::fake(Config::get('image.disk'));
    $file = File::fake()->image(fake()->word().'.jpg');
    $fsFile = $fs->putFile('', $file);

    $facet = Arr::random(ImageFacet::cases());

    $image = Image::factory()->createOne([
        Image::ATTRIBUTE_FACET => $facet->value,
        Image::ATTRIBUTE_PATH => $fsFile,
    ]);

    $image->delete();

    expect($fs->exists($image->path))->toBeTrue();
});

test('image storage force deletion', function (): void {
    Event::fakeExcept(ImageForceDeleting::class);

    $fs = Storage::fake(Config::get('image.disk'));
    $file = File::fake()->image(fake()->word().'.jpg');
    $fsFile = $fs->putFile('', $file);

    $facet = Arr::random(ImageFacet::cases());

    $image = Image::factory()->createOne([
        Image::ATTRIBUTE_FACET => $facet->value,
        Image::ATTRIBUTE_PATH => $fsFile,
    ]);

    $image->forceDelete();

    expect($fs->exists($image->path))->toBeFalse();
});
