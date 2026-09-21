<?php

declare(strict_types=1);

use App\Constants\Config\AudioConstants;
use App\Events\Wiki\Audio\AudioForceDeleting;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

pest()->use(WithFaker::class);

test('nameable', function (): void {
    $audio = Audio::factory()->createOne();

    expect($audio->getName())->toBeString();
});

test('has subtitle', function (): void {
    $audio = Audio::factory()->createOne();

    expect($audio->getSubtitle())->toBeString();
});

test('videos', function (): void {
    $videoCount = fake()->randomDigitNotNull();

    $audio = Audio::factory()
        ->has(Video::factory()->count($videoCount))
        ->createOne();

    expect($audio->videos())->toBeInstanceOf(HasMany::class);
    expect($audio->videos()->count())->toEqual($videoCount);
    expect($audio->videos()->first())->toBeInstanceOf(Video::class);
});

test('audio storage deletion', function (): void {
    $fs = Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());
    $fsFile = $fs->putFile('', $file);

    $audio = Audio::factory()->createOne([
        Audio::ATTRIBUTE_PATH => $fsFile,
    ]);

    $audio->delete();

    expect($fs->exists($audio->path))->toBeTrue();
});

test('audio storage force deletion', function (): void {
    Event::fakeExcept(AudioForceDeleting::class);

    $fs = Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());
    $fsFile = $fs->putFile('', $file);

    $audio = Audio::factory()->createOne([
        Audio::ATTRIBUTE_PATH => $fsFile,
    ]);

    $audio->forceDelete();

    expect($fs->exists($audio->path))->toBeFalse();
});
