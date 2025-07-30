<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\UploadedFileAction;
use App\Actions\Storage\Wiki\Video\UploadVideoAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    Config::set(VideoConstants::DISKS_QUALIFIED, []);
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());

    $action = new UploadVideoAction($file, fake()->word());

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    $this->assertTrue($result->hasFailed());
});

test('passed', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);

    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());

    $action = new UploadVideoAction($file, fake()->word());

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    $this->assertTrue($result->getStatus() === ActionStatus::PASSED);
});

test('uploaded to disk', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);

    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());

    $action = new UploadVideoAction($file, fake()->word());

    $action->handle();

    $this->assertCount(1, Storage::disk(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED))->allFiles());
});

test('created video', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);

    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());

    Process::fake([
        UploadedFileAction::formatFfprobeCommand($file) => Process::result(json_encode([
            'streams' => [
                0 => [
                    'height' => fake()->randomNumber(),
                ],
            ],
        ])),
    ]);

    $action = new UploadVideoAction($file, fake()->word());

    $result = $action->handle();

    $action->then($result);

    $this->assertDatabaseCount(Video::class, 1);
});

test('sets attributes', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);

    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());

    Process::fake([
        UploadedFileAction::formatFfprobeCommand($file) => Process::result(json_encode([
            'streams' => [
                0 => [
                    'height' => fake()->randomNumber(),
                ],
            ],
        ])),
    ]);

    $overlap = Arr::random(VideoOverlap::cases());
    $source = Arr::random(VideoSource::cases());

    $attributes = [
        Video::ATTRIBUTE_NC => fake()->boolean(),
        Video::ATTRIBUTE_SUBBED => fake()->boolean(),
        Video::ATTRIBUTE_LYRICS => fake()->boolean(),
        Video::ATTRIBUTE_UNCEN => fake()->boolean(),
        Video::ATTRIBUTE_OVERLAP => $overlap->value,
        Video::ATTRIBUTE_SOURCE => $source->value,
    ];

    $action = new UploadVideoAction($file, fake()->word(), $attributes);

    $result = $action->handle();

    $action->then($result);

    $this->assertDatabaseHas(Video::class, $attributes);
});

test('attaches entry', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);

    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());

    Process::fake([
        UploadedFileAction::formatFfprobeCommand($file) => Process::result(json_encode([
            'streams' => [
                0 => [
                    'height' => fake()->randomNumber(),
                ],
            ],
        ])),
    ]);

    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->createOne();

    $action = new UploadVideoAction(file: $file, path: fake()->word(), entry: $entry);

    $result = $action->handle();

    $action->then($result);

    $this->assertDatabaseCount(AnimeThemeEntryVideo::class, 1);
});

test('associates script', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
    Config::set(VideoConstants::DISKS_QUALIFIED, [Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED)]);
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.webm', fake()->randomDigitNotNull());
    $script = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    Process::fake([
        UploadedFileAction::formatFfprobeCommand($file) => Process::result(json_encode([
            'streams' => [
                0 => [
                    'height' => fake()->randomNumber(),
                ],
            ],
        ])),
    ]);

    $action = new UploadVideoAction(file: $file, path: fake()->word(), script: $script);

    $result = $action->handle();

    $action->then($result);

    /** @var Video $video */
    $video = Video::query()->first();

    $this->assertNotNull($video);

    $this->assertDatabaseHas(VideoScript::class, [VideoScript::ATTRIBUTE_VIDEO => $video->video_id]);
});
