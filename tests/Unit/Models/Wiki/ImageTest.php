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

uses(WithFaker::class);

test('casts facet to enum', function () {
    $image = Image::factory()->createOne();

    $facet = $image->facet;

    $this->assertInstanceOf(ImageFacet::class, $facet);
});

test('nameable', function () {
    $image = Image::factory()->createOne();

    $this->assertIsString($image->getName());
});

test('has subtitle', function () {
    $image = Image::factory()->createOne();

    $this->assertIsString($image->getSubtitle());
});

test('anime', function () {
    $animeCount = fake()->randomDigitNotNull();

    $image = Image::factory()
        ->has(Anime::factory()->count($animeCount))
        ->createOne();

    $this->assertInstanceOf(MorphToMany::class, $image->anime());
    $this->assertEquals($animeCount, $image->anime()->count());
    $this->assertInstanceOf(Anime::class, $image->anime()->first());
    $this->assertEquals(Imageable::class, $image->anime()->getPivotClass());
});

test('artists', function () {
    $artistCount = fake()->randomDigitNotNull();

    $image = Image::factory()
        ->has(Artist::factory()->count($artistCount))
        ->createOne();

    $this->assertInstanceOf(MorphToMany::class, $image->artists());
    $this->assertEquals($artistCount, $image->artists()->count());
    $this->assertInstanceOf(Artist::class, $image->artists()->first());
    $this->assertEquals(Imageable::class, $image->artists()->getPivotClass());
});

test('studios', function () {
    $studioCount = fake()->randomDigitNotNull();

    $image = Image::factory()
        ->has(Studio::factory()->count($studioCount))
        ->createOne();

    $this->assertInstanceOf(MorphToMany::class, $image->studios());
    $this->assertEquals($studioCount, $image->studios()->count());
    $this->assertInstanceOf(Studio::class, $image->studios()->first());
    $this->assertEquals(Imageable::class, $image->studios()->getPivotClass());
});

test('playlists', function () {
    $playlistCount = fake()->randomDigitNotNull();

    $image = Image::factory()
        ->has(Playlist::factory()->count($playlistCount))
        ->createOne();

    $this->assertInstanceOf(MorphToMany::class, $image->playlists());
    $this->assertEquals($playlistCount, $image->playlists()->count());
    $this->assertInstanceOf(Playlist::class, $image->playlists()->first());
    $this->assertEquals(Imageable::class, $image->playlists()->getPivotClass());
});

test('image storage deletion', function () {
    $fs = Storage::fake(Config::get('image.disk'));
    $file = File::fake()->image(fake()->word().'.jpg');
    $fsFile = $fs->putFile('', $file);

    $facet = Arr::random(ImageFacet::cases());

    $image = Image::factory()->createOne([
        Image::ATTRIBUTE_FACET => $facet->value,
        Image::ATTRIBUTE_PATH => $fsFile,
    ]);

    $image->delete();

    $this->assertTrue($fs->exists($image->path));
});

test('image storage force deletion', function () {
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

    $this->assertFalse($fs->exists($image->path));
});
