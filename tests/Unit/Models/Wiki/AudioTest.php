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

    $this->assertIsString($audio->getName());
});

test('has subtitle', function () {
    $audio = Audio::factory()->createOne();

    $this->assertIsString($audio->getSubtitle());
});

test('videos', function () {
    $videoCount = fake()->randomDigitNotNull();

    $audio = Audio::factory()
        ->has(Video::factory()->count($videoCount))
        ->createOne();

    $this->assertInstanceOf(HasMany::class, $audio->videos());
    $this->assertEquals($videoCount, $audio->videos()->count());
    $this->assertInstanceOf(Video::class, $audio->videos()->first());
});

test('views', function () {
    $audio = Audio::factory()->createOne();

    views($audio)->record();

    $this->assertInstanceOf(MorphMany::class, $audio->views());
    $this->assertEquals(1, $audio->views()->count());
    $this->assertInstanceOf(View::class, $audio->views()->first());
});

test('audio storage deletion', function () {
    $fs = Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());
    $fsFile = $fs->putFile('', $file);

    $audio = Audio::factory()->createOne([
        Audio::ATTRIBUTE_PATH => $fsFile,
    ]);

    $audio->delete();

    $this->assertTrue($fs->exists($audio->path));
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

    $this->assertFalse($fs->exists($audio->path));
});
