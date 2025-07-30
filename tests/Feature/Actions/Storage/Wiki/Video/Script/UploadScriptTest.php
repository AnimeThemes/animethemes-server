<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\Video\Script\UploadScriptAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    Config::set(VideoConstants::SCRIPT_DISK_QUALIFIED, []);
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $action = new UploadScriptAction($file, fake()->word());

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    static::assertTrue($result->hasFailed());
});

test('passed', function () {
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $action = new UploadScriptAction($file, fake()->word());

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    static::assertTrue($result->getStatus() === ActionStatus::PASSED);
});

test('uploaded to disk', function () {
    $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $action = new UploadScriptAction($file, fake()->word());

    $action->handle();

    static::assertCount(1, $fs->allFiles());
});

test('created video', function () {
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $action = new UploadScriptAction($file, fake()->word());

    $result = $action->handle();

    $action->then($result);

    static::assertDatabaseCount(VideoScript::class, 1);
});

test('attaches video', function () {
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $video = Video::factory()->createOne();

    $action = new UploadScriptAction($file, fake()->word(), $video);

    $result = $action->handle();

    $action->then($result);

    static::assertTrue($video->videoscript()->exists());
});
