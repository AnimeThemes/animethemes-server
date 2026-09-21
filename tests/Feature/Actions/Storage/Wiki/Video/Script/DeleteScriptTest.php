<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\Video\Script\DeleteScriptAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

pest()->use(WithFaker::class);

test('default', function (): void {
    Config::set(VideoConstants::SCRIPT_DISK_QUALIFIED, []);
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $script = VideoScript::factory()->createOne();

    $action = new DeleteScriptAction($script);

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    expect($result->hasFailed())->toBeTrue();
});

test('passed', function (): void {
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $script = VideoScript::factory()->createOne([
        VideoScript::ATTRIBUTE_PATH => $file->path(),
    ]);

    $action = new DeleteScriptAction($script);

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    expect($result->getStatus())->toBe(ActionStatus::PASSED);
});

test('deleted from disk', function (): void {
    $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $script = VideoScript::factory()->createOne([
        VideoScript::ATTRIBUTE_PATH => $file->path(),
    ]);

    $action = new DeleteScriptAction($script);

    $action->handle();

    expect($fs->allFiles())->toBeEmpty();
});

test('video deleted', function (): void {
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $script = VideoScript::factory()->createOne([
        VideoScript::ATTRIBUTE_PATH => $file->path(),
    ]);

    $action = new DeleteScriptAction($script);

    $result = $action->handle();

    $action->then($result);

    $this->assertSoftDeleted($script);
});
