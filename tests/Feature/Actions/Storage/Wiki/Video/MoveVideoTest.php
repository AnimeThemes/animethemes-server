<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\Video\MoveVideoAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Testing\File;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    Config::set(VideoConstants::DISKS_QUALIFIED, []);
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    $video = Video::factory()->createOne();

    $action = new MoveVideoAction($video, fake()->word());

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    $this->assertTrue($result->hasFailed());
});

test('passed', function () {
    /** @var FilesystemAdapter $fs */
    $fs = Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);

    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());

    $directory = fake()->unique()->word();

    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
        Video::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
        Video::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
        Video::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
    ]);

    $action = new MoveVideoAction($video, Str::replace($directory, fake()->unique()->word(), $video->path));

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
});

test('moved in disk', function () {
    /** @var FilesystemAdapter $fs */
    $fs = Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);

    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());

    $directory = fake()->unique()->word();

    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
        Video::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
        Video::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
        Video::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
    ]);

    $from = $video->path();
    $to = Str::replace($directory, fake()->unique()->word(), $video->path);

    $action = new MoveVideoAction($video, $to);

    $action->handle();

    $fs->assertMissing($from);
    $fs->assertExists($to);
});

test('video updated', function () {
    /** @var FilesystemAdapter $fs */
    $fs = Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);

    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());

    $directory = fake()->unique()->word();

    $video = Video::factory()->createOne([
        Video::ATTRIBUTE_BASENAME => FileFacade::basename($file->path()),
        Video::ATTRIBUTE_FILENAME => FileFacade::name($file->path()),
        Video::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
        Video::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
    ]);

    $to = Str::replace($directory, fake()->unique()->word(), $video->path);

    $action = new MoveVideoAction($video, $to);

    $result = $action->handle();

    $action->then($result);

    $this->assertDatabaseHas(Video::class, [Video::ATTRIBUTE_PATH => $to]);
});
