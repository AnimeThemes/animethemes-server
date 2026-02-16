<?php

declare(strict_types=1);

use App\Events\Pivot\Wiki\AnimeThemeEntryVideo\AnimeThemeEntryVideoCreated;
use App\Events\Pivot\Wiki\AnimeThemeEntryVideo\AnimeThemeEntryVideoDeleted;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Event;

test('anime theme entry video created event dispatched', function () {
    $video = Video::factory()->createOne();
    $entry = AnimeThemeEntry::factory()->createOne();

    $video->animethemeentries()->attach($entry);

    Event::assertDispatched(AnimeThemeEntryVideoCreated::class);
});

test('anime theme entry video deleted event dispatched', function () {
    $video = Video::factory()->createOne();
    $entry = AnimeThemeEntry::factory()->createOne();

    $video->animethemeentries()->attach($entry);
    $video->animethemeentries()->detach($entry);

    Event::assertDispatched(AnimeThemeEntryVideoDeleted::class);
});

test('anime theme entry video created event update playlist tracks', function () {
    $video = Video::factory()->createOne();
    $entry = AnimeThemeEntry::factory()->createOne();

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->for($video)
        ->createOne();

    $video->animethemeentries()->attach($entry);

    Event::assertDispatched(AnimeThemeEntryVideoCreated::class, function (AnimeThemeEntryVideoCreated $event) use ($entry, $track) {
        $event->updatePlaylistTracks();

        return $track->refresh()->animethemeentry()->is($entry);
    });
});

test('anime theme entry video deleted event update playlist tracks', function () {
    $video = Video::factory()->createOne();

    $entry = AnimeThemeEntry::factory()->createOne();

    $secondEntry = AnimeThemeEntry::factory()->createOne();

    $video->animethemeentries()->attach($entry);

    $track = PlaylistTrack::factory()
        ->for(Playlist::factory())
        ->for($video)
        ->for($entry)
        ->createOne();

    $video->animethemeentries()->attach($secondEntry);
    $video->animethemeentries()->detach($entry);

    Event::assertDispatched(AnimeThemeEntryVideoDeleted::class, function (AnimeThemeEntryVideoDeleted $event) use ($secondEntry, $track) {
        $event->updatePlaylistTracks();

        return $track->refresh()->animethemeentry()->is($secondEntry);
    });
});
