<?php

declare(strict_types=1);

use App\Events\Wiki\Video\Script\VideoScriptCreated;
use App\Events\Wiki\Video\Script\VideoScriptDeleted;
use App\Events\Wiki\Video\Script\VideoScriptRestored;
use App\Events\Wiki\Video\Script\VideoScriptUpdated;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('video script created event dispatched', function (): void {
    VideoScript::factory()->createOne();

    Event::assertDispatched(VideoScriptCreated::class);
});

test('video script deleted event dispatched', function (): void {
    $script = VideoScript::factory()->createOne();

    $script->delete();

    Event::assertDispatched(VideoScriptDeleted::class);
});

test('video script restored event dispatched', function (): void {
    $script = VideoScript::factory()->createOne();

    $script->restore();

    Event::assertDispatched(VideoScriptRestored::class);
});

test('video script restores quietly', function (): void {
    $script = VideoScript::factory()->createOne();

    $script->restore();

    Event::assertNotDispatched(VideoScriptUpdated::class);
});

test('video script updated event dispatched', function (): void {
    $script = VideoScript::factory()->createOne();
    $changes = VideoScript::factory()->makeOne();

    $script->fill($changes->getAttributes());
    $script->save();

    Event::assertDispatched(VideoScriptUpdated::class);
});

test('video script updated event embed fields', function (): void {
    $script = VideoScript::factory()->createOne();
    $changes = VideoScript::factory()->makeOne();

    $script->fill($changes->getAttributes());
    $script->save();

    Event::assertDispatched(VideoScriptUpdated::class, function (VideoScriptUpdated $event): bool {
        $message = $event->getDiscordMessage();

        return filled(Arr::get($message->embed, 'fields'));
    });
});
