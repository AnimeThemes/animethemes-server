<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Pivot\Wiki;

use App\Events\Pivot\Wiki\AnimeThemeEntryVideo\AnimeThemeEntryVideoCreated;
use App\Events\Pivot\Wiki\AnimeThemeEntryVideo\AnimeThemeEntryVideoDeleted;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class AnimeThemeEntryVideoTest.
 */
class AnimeThemeEntryVideoTest extends TestCase
{
    /**
     * When a Video is attached to an AnimeThemeEntry or vice versa, an AnimeThemeEntryVideoTest event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeThemeEntryVideoCreatedEventDispatched(): void
    {
        $video = Video::factory()->createOne();
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $video->animethemeentries()->attach($entry);

        Event::assertDispatched(AnimeThemeEntryVideoCreated::class);
    }

    /**
     * When a Video is detached from an AnimeThemeEntry or vice versa, an AnimeThemeEntryVideoDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testAnimeThemeEntryVideoDeletedEventDispatched(): void
    {
        $video = Video::factory()->createOne();
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $video->animethemeentries()->attach($entry);
        $video->animethemeentries()->detach($entry);

        Event::assertDispatched(AnimeThemeEntryVideoDeleted::class);
    }

    /**
     * When a Video is attached to an AnimeThemeEntry, the playlist tracks should be updated.
     *
     * @return void
     */
    public function testAnimeThemeEntryVideoCreatedEventUpdatePlaylistTracks(): void
    {
        $video = Video::factory()->createOne();
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $video->animethemeentries()->attach($entry);

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->for($video)
            ->createOne();

        Event::assertDispatched(AnimeThemeEntryVideoCreated::class, function (AnimeThemeEntryVideoCreated $event) use ($entry, $track) {
            $event->updatePlaylistTracks();

            return $track->refresh()->animethemeentry()->is($entry);
        });
    }

    /**
     * When a Video is detached from an AnimeThemeEntry, the playlist tracks should be updated.
     *
     * @return void
     */
    public function testAnimeThemeEntryVideoDeletedEventUpdatePlaylistTracks(): void
    {
        $video = Video::factory()->createOne();

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $secondEntry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

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
    }
}
