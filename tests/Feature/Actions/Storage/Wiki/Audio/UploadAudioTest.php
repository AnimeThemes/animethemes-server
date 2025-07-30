<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\Audio\UploadAudioAction;
use App\Constants\Config\AudioConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Audio;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    Config::set(AudioConstants::DISKS_QUALIFIED, []);
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());

    $action = new UploadAudioAction($file, fake()->word());

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    $this->assertTrue($result->hasFailed());
});

test('passed', function () {
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
    Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);

    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());

    $action = new UploadAudioAction($file, fake()->word());

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
});

test('uploaded to disk', function () {
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
    Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);

    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());

    $action = new UploadAudioAction($file, fake()->word());

    $action->handle();

    $this->assertCount(1, Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
});

test('created audio', function () {
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
    Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);

    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());

    $action = new UploadAudioAction($file, fake()->word());

    $result = $action->handle();

    $action->then($result);

    $this->assertDatabaseCount(Audio::class, 1);
});
