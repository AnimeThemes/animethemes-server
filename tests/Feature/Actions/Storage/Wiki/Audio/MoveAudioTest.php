<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\Audio\MoveAudioAction;
use App\Constants\Config\AudioConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Audio;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Testing\File;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    Config::set(AudioConstants::DISKS_QUALIFIED, []);
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    $audio = Audio::factory()->createOne();

    $action = new MoveAudioAction($audio, fake()->word());

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    static::assertTrue($result->hasFailed());
});

test('passed', function () {
    /** @var FilesystemAdapter $fs */
    $fs = Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
    Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);

    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());

    $directory = fake()->unique()->word();

    $audio = Audio::factory()->createOne([
        Audio::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
        Audio::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
        Audio::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
        Audio::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
    ]);

    $action = new MoveAudioAction($audio, Str::replace($directory, fake()->unique()->word(), $audio->path));

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    static::assertTrue($result->getStatus() === ActionStatus::PASSED);
});

test('moved in disk', function () {
    /** @var FilesystemAdapter $fs */
    $fs = Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
    Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);

    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());

    $directory = fake()->unique()->word();

    $audio = Audio::factory()->createOne([
        Audio::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
        Audio::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
        Audio::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
        Audio::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
    ]);

    $from = $audio->path();
    $to = Str::replace($directory, fake()->unique()->word(), $audio->path);

    $action = new MoveAudioAction($audio, $to);

    $action->handle();

    $fs->assertMissing($from);
    $fs->assertExists($to);
});

test('audio updated', function () {
    /** @var FilesystemAdapter $fs */
    $fs = Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
    Config::set(AudioConstants::DISKS_QUALIFIED, [Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED)]);

    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());

    $directory = fake()->unique()->word();

    $audio = Audio::factory()->createOne([
        Audio::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
        Audio::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
        Audio::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
        Audio::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
    ]);

    $to = Str::replace($directory, fake()->unique()->word(), $audio->path);

    $action = new MoveAudioAction($audio, $to);

    $result = $action->handle();

    $action->then($result);

    static::assertDatabaseHas(Audio::class, [Audio::ATTRIBUTE_PATH => $to]);
});
