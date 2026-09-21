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

pest()->use(WithFaker::class);

test('nameable', function (): void {
    $script = VideoScript::factory()->createOne();

    expect($script->getName())->toBeString();
});

test('has subtitle', function (): void {
    $script = VideoScript::factory()
        ->for(Video::factory())
        ->createOne();

    expect($script->getSubtitle())->toBeString();
});

test('video', function (): void {
    $script = VideoScript::factory()
        ->for(Video::factory())
        ->createOne();

    expect($script->video())->toBeInstanceOf(BelongsTo::class);
    expect($script->video()->first())->toBeInstanceOf(Video::class);
});

test('script storage deletion', function (): void {
    $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));
    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());
    $fsFile = $fs->putFile('', $file);

    $script = VideoScript::factory()->createOne([
        VideoScript::ATTRIBUTE_PATH => $fsFile,
    ]);

    $script->delete();

    expect($fs->exists($script->path))->toBeTrue();
});

test('script storage force deletion', function (): void {
    Event::fakeExcept(VideoScriptForceDeleting::class);

    $fs = Storage::fake(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));
    $file = File::fake()->create(fake()->word().'.ogg', fake()->randomDigitNotNull());
    $fsFile = $fs->putFile('', $file);

    $script = VideoScript::factory()->createOne([
        VideoScript::ATTRIBUTE_PATH => $fsFile,
    ]);

    $script->forceDelete();

    expect($fs->exists($script->path))->toBeFalse();
});
