<?php

declare(strict_types=1);

use App\Constants\Config\VideoConstants;
use App\Events\Wiki\Video\Script\VideoScriptForceDeleting;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

uses(WithFaker::class);

test('nameable', function () {
    $script = VideoScript::factory()->createOne();

    $this->assertIsString($script->getName());
});

test('has subtitle', function () {
    $script = VideoScript::factory()
        ->for(Video::factory())
        ->createOne();

    $this->assertIsString($script->getSubtitle());
});

test('video', function () {
    $script = VideoScript::factory()
        ->for(Video::factory())
        ->createOne();

    $this->assertInstanceOf(BelongsTo::class, $script->video());
    $this->assertInstanceOf(Video::class, $script->video()->first());
});

test('script storage deletion', function () {
    $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));
    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());
    $fsFile = $fs->putFile('', $file);

    $script = VideoScript::factory()->createOne([
        VideoScript::ATTRIBUTE_PATH => $fsFile,
    ]);

    $script->delete();

    $this->assertTrue($fs->exists($script->path));
});

test('script storage force deletion', function () {
    Event::fakeExcept(VideoScriptForceDeleting::class);

    $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));
    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());
    $fsFile = $fs->putFile('', $file);

    $script = VideoScript::factory()->createOne([
        VideoScript::ATTRIBUTE_PATH => $fsFile,
    ]);

    $script->forceDelete();

    $this->assertFalse($fs->exists($script->path));
});
