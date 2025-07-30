<?php

declare(strict_types=1);

use App\Events\Wiki\Video\VideoCreated;
use App\Events\Wiki\Video\VideoDeleted;
use App\Events\Wiki\Video\VideoRestored;
use App\Events\Wiki\Video\VideoUpdated;
use App\Models\Wiki\Video;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('video created event dispatched', function () {
    Video::factory()->createOne();

    Event::assertDispatched(VideoCreated::class);
});

test('video deleted event dispatched', function () {
    $video = Video::factory()->createOne();

    $video->delete();

    Event::assertDispatched(VideoDeleted::class);
});

test('video restored event dispatched', function () {
    $video = Video::factory()->createOne();

    $video->restore();

    Event::assertDispatched(VideoRestored::class);
});

test('video restores quietly', function () {
    $video = Video::factory()->createOne();

    $video->restore();

    Event::assertNotDispatched(VideoUpdated::class);
});

test('video updated event dispatched', function () {
    $video = Video::factory()->createOne();
    $changes = Video::factory()->makeOne();

    $video->fill($changes->getAttributes());
    $video->save();

    Event::assertDispatched(VideoUpdated::class);
});

test('video updated event embed fields', function () {
    $video = Video::factory()->createOne();
    $changes = Video::factory()->makeOne();

    $video->fill($changes->getAttributes());
    $video->save();

    Event::assertDispatched(VideoUpdated::class, function (VideoUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
