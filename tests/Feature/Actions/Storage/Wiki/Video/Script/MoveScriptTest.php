<?php

declare(strict_types=1);

use App\Actions\Storage\Wiki\Video\Script\MoveScriptAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    Config::set(VideoConstants::SCRIPT_DISK_QUALIFIED, []);
    Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $script = VideoScript::factory()->createOne();

    $action = new MoveScriptAction($script, fake()->word());

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    static::assertTrue($result->hasFailed());
});

test('passed', function () {
    /** @var FilesystemAdapter $fs */
    $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $directory = fake()->unique()->word();

    $script = VideoScript::factory()->createOne([
        VideoScript::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
    ]);

    $action = new MoveScriptAction($script, Str::replace($directory, fake()->unique()->word(), $script->path));

    $storageResults = $action->handle();

    $result = $storageResults->toActionResult();

    static::assertTrue($result->getStatus() === ActionStatus::PASSED);
});

test('moved in disk', function () {
    /** @var FilesystemAdapter $fs */
    $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $directory = fake()->unique()->word();

    $script = VideoScript::factory()->createOne([
        VideoScript::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
    ]);

    $from = $script->path;
    $to = Str::replace($directory, fake()->unique()->word(), $script->path);

    $action = new MoveScriptAction($script, $to);

    $action->handle();

    $fs->assertMissing($from);
    $fs->assertExists($to);
});

test('script updated', function () {
    /** @var FilesystemAdapter $fs */
    $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

    $file = File::fake()->create(fake()->word().'.txt', fake()->randomDigitNotNull());

    $directory = fake()->unique()->word();

    $script = VideoScript::factory()->createOne([
        VideoScript::ATTRIBUTE_PATH => $fs->putFileAs($directory, $file, $file->getClientOriginalName()),
    ]);

    $to = Str::replace($directory, fake()->unique()->word(), $script->path);

    $action = new MoveScriptAction($script, $to);

    $result = $action->handle();

    $action->then($result);

    static::assertDatabaseHas(VideoScript::class, [VideoScript::ATTRIBUTE_PATH => $to]);
});
