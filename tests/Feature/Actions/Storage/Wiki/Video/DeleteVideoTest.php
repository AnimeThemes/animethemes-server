<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\Video\DeleteVideoAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;
use Illuminate\Http\Testing\File;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    Config::set(VideoConstants::DISKS_QUALIFIED, []);
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    $video = Video::factory()->createOne();

    $action = new DeleteVideoAction($video);

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    $this->assertTrue($result->hasFailed());
});

test('passed', function () {
    Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());

    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
        Video::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
        Video::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
        Video::ATTRIBUTE_PATH => $file->path(),
    ]);

    $action = new DeleteVideoAction($video);

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
});

test('deleted from disk', function () {
    Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());

    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
        Video::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
        Video::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
        Video::ATTRIBUTE_PATH => $file->path(),
    ]);

    $action = new DeleteVideoAction($video);

    $action->handle();

    $this->assertEmpty(Storage::disk(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
});

test('video deleted', function () {
    Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());

    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
        Video::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
        Video::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
        Video::ATTRIBUTE_PATH => $file->path(),
    ]);

    $action = new DeleteVideoAction($video);

    $result = $action->handle();

    $action->then($result);

    $this->assertSoftDeleted($video);
});
