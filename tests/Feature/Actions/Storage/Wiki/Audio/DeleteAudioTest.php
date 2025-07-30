<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\Audio\DeleteAudioAction;
use App\Constants\Config\AudioConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Audio;
use Illuminate\Http\Testing\File;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    Config::set(AudioConstants::DISKS_QUALIFIED, []);
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $audio = Audio::factory()->createOne();

    $action = new DeleteAudioAction($audio);

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    static::assertTrue($result->hasFailed());
});

test('passed', function () {
    Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());

    $audio = Audio::factory()->createOne([
        Audio::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
        Audio::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
        Audio::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
        Audio::ATTRIBUTE_PATH => $file->path(),
    ]);

    $action = new DeleteAudioAction($audio);

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    static::assertTrue($result->getStatus() === ActionStatus::PASSED);
});

test('deleted from disk', function () {
    Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());

    $audio = Audio::factory()->createOne([
        Audio::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
        Audio::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
        Audio::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
        Audio::ATTRIBUTE_PATH => $file->path(),
    ]);

    $action = new DeleteAudioAction($audio);

    $action->handle();

    static::assertEmpty(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
});

test('audio deleted', function () {
    Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());

    $audio = Audio::factory()->createOne([
        Audio::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
        Audio::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
        Audio::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
        Audio::ATTRIBUTE_PATH => $file->path(),
    ]);

    $action = new DeleteAudioAction($audio);

    $result = $action->handle();

    $action->then($result);

    static::assertSoftDeleted($audio);
});
