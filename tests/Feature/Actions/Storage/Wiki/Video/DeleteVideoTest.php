<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\Video\DeleteVideoAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage;

pest()->use(WithFaker::class);

test('default', function (): void {
    Config::set(VideoConstants::DISKS_QUALIFIED, []);
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    $video = Video::factory()->createOne();

    $action = new DeleteVideoAction($video);

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    expect($result->hasFailed())->toBeTrue();
});

test('passed', function (): void {
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

    expect($result->getStatus())->toBe(ActionStatus::PASSED);
});

test('deleted from disk', function (): void {
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

    expect(Storage::disk(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED))->allFiles())->toBeEmpty();
});

test('video deleted', function (): void {
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
