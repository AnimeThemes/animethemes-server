<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\Video\Script\UploadScriptAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

pest()->use(WithFaker::class);

test('default', function (): void {
    Config::set(VideoConstants::SCRIPT_DISK_QUALIFIED, []);
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $action = new UploadScriptAction($file, fake()->word());

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    expect($result->hasFailed())->toBeTrue();
});

test('passed', function (): void {
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $action = new UploadScriptAction($file, fake()->word());

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    expect($result->getStatus())->toBe(ActionStatus::PASSED);
});

test('uploaded to disk', function (): void {
    $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $action = new UploadScriptAction($file, fake()->word());

    $action->handle();

    expect($fs->allFiles())->toHaveCount(1);
});

test('created video', function (): void {
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $action = new UploadScriptAction($file, fake()->word());

    $result = $action->handle();

    $action->then($result);

    $this->assertDatabaseCount(VideoScript::class, 1);
});

test('attaches video', function (): void {
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $video = Video::factory()->createOne();

    $action = new UploadScriptAction($file, fake()->word(), $video);

    $result = $action->handle();

    $action->then($result);

    expect($video->videoscript()->exists())->toBeTrue();
});
