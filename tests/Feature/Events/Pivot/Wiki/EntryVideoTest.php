<?php

declare(strict_types=1);

use App\Events\Pivot\Wiki\EntryVideo\EntryVideoCreated;
use App\Events\Pivot\Wiki\EntryVideo\EntryVideoDeleted;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Event;

test('anime theme entry video created event dispatched', function (): void {
    $video = Video::factory()->createOne();
    $entry = Entry::factory()->createOne();

    $video->animethemeentries()->attach($entry);

    Event::assertDispatched(EntryVideoCreated::class);
});

test('anime theme entry video deleted event dispatched', function (): void {
    $video = Video::factory()->createOne();
    $entry = Entry::factory()->createOne();

    $video->animethemeentries()->attach($entry);
    $video->animethemeentries()->detach($entry);

    Event::assertDispatched(EntryVideoDeleted::class);
});

test('anime theme entry video created event update playlist tracks', function (): void {
    $video = Video::factory()->createOne();
    $entry = Entry::factory()->createOne();

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->for($video)
        ->createOne();

    $video->animethemeentries()->attach($entry);

    Event::assertDispatched(EntryVideoCreated::class, function (EntryVideoCreated $event) use ($entry, $track) {
        $event->updatePlaylistTracks();

        return $track->refresh()->animethemeentry()->is($entry);
    });
});

test('anime theme entry video deleted event update playlist tracks', function (): void {
    $video = Video::factory()->createOne();

    $entry = Entry::factory()->createOne();

    $secondEntry = Entry::factory()->createOne();

    $video->animethemeentries()->attach($entry);

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->for($video)
        ->for($entry)
        ->createOne();

    $video->animethemeentries()->attach($secondEntry);
    $video->animethemeentries()->detach($entry);

    Event::assertDispatched(EntryVideoDeleted::class, function (EntryVideoDeleted $event) use ($secondEntry, $track) {
        $event->updatePlaylistTracks();

        return $track->refresh()->animethemeentry()->is($secondEntry);
    });
});
