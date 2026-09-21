<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\Audio\DeleteAudioAction;
use App\Constants\Config\AudioConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Audio;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage;

pest()->use(WithFaker::class);

test('default', function (): void {
    Config::set(AudioConstants::DISKS_QUALIFIED, []);
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $audio = Audio::factory()->createOne();

    $action = new DeleteAudioAction($audio);

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    expect($result->hasFailed())->toBeTrue();
});

test('passed', function (): void {
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

    expect($result->getStatus())->toBe(ActionStatus::PASSED);
});

test('deleted from disk', function (): void {
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

    expect(Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles())->toBeEmpty();
});

test('audio deleted', function (): void {
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

    $this->assertSoftDeleted($audio);
});
