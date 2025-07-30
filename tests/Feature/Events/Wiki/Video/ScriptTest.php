<?php

declare(strict_types=1);

use App\Events\Wiki\Video\Script\VideoScriptCreated;
use App\Events\Wiki\Video\Script\VideoScriptDeleted;
use App\Events\Wiki\Video\Script\VideoScriptRestored;
use App\Events\Wiki\Video\Script\VideoScriptUpdated;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('video script created event dispatched', function () {
    VideoScript::factory()->createOne();

    Event::assertDispatched(VideoScriptCreated::class);
});

test('video script deleted event dispatched', function () {
    $script = VideoScript::factory()->createOne();

    $script->delete();

    Event::assertDispatched(VideoScriptDeleted::class);
});

test('video script restored event dispatched', function () {
    $script = VideoScript::factory()->createOne();

    $script->restore();

    Event::assertDispatched(VideoScriptRestored::class);
});

test('video script restores quietly', function () {
    $script = VideoScript::factory()->createOne();

    $script->restore();

    Event::assertNotDispatched(VideoScriptUpdated::class);
});

test('video script updated event dispatched', function () {
    $script = VideoScript::factory()->createOne();
    $changes = VideoScript::factory()->makeOne();

    $script->fill($changes->getAttributes());
    $script->save();

    Event::assertDispatched(VideoScriptUpdated::class);
});

test('video script updated event embed fields', function () {
    $script = VideoScript::factory()->createOne();
    $changes = VideoScript::factory()->makeOne();

    $script->fill($changes->getAttributes());
    $script->save();

    Event::assertDispatched(VideoScriptUpdated::class, function (VideoScriptUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
