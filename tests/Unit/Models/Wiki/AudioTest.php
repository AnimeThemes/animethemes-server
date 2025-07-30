<?php

declare(strict_types=1);

use App\Constants\Config\AudioConstants;
use App\Events\Wiki\Audio\AudioForceDeleting;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use CyrildeWit\EloquentViewable\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

uses(WithFaker::class);

test('nameable', function () {
    $audio = Audio::factory()->createOne();

    static::assertIsString($audio->getName());
});

test('has subtitle', function () {
    $audio = Audio::factory()->createOne();

    static::assertIsString($audio->getSubtitle());
});

test('videos', function () {
    $videoCount = fake()->randomDigitNotNull();

    $audio = Audio::factory()
        ->has(Video::factory()->count($videoCount))
        ->createOne();

    static::assertInstanceOf(HasMany::class, $audio->videos());
    static::assertEquals($videoCount, $audio->videos()->count());
    static::assertInstanceOf(Video::class, $audio->videos()->first());
});

test('views', function () {
    $audio = Audio::factory()->createOne();

    views($audio)->record();

    static::assertInstanceOf(MorphMany::class, $audio->views());
    static::assertEquals(1, $audio->views()->count());
    static::assertInstanceOf(View::class, $audio->views()->first());
});

test('audio storage deletion', function () {
    $fs = Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());
    $fsFile = $fs->putFile('', $file);

    $audio = Audio::factory()->createOne([
        Audio::ATTRIBUTE_PATH => $fsFile,
    ]);

    $audio->delete();

    static::assertTrue($fs->exists($audio->path));
});

test('audio storage force deletion', function () {
    Event::fakeExcept(AudioForceDeleting::class);

    $fs = Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());
    $fsFile = $fs->putFile('', $file);

    $audio = Audio::factory()->createOne([
        Audio::ATTRIBUTE_PATH => $fsFile,
    ]);

    $audio->forceDelete();

    static::assertFalse($fs->exists($audio->path));
});
