<?php

namespace Tests\Unit\Events;

use App\Events\Anime\AnimeCreated;
use App\Events\Anime\AnimeUpdated;
use App\Events\Entry\EntryCreated;
use App\Events\Entry\EntryDeleted;
use App\Events\Entry\EntryDeleting;
use App\Events\Entry\EntryRestored;
use App\Events\Entry\EntryUpdated;
use App\Events\Pivot\ArtistSong\ArtistSongCreated;
use App\Events\Pivot\ArtistSong\ArtistSongDeleted;
use App\Events\Pivot\ArtistSong\ArtistSongUpdated;
use App\Events\Pivot\VideoEntry\VideoEntryCreated;
use App\Events\Pivot\VideoEntry\VideoEntryDeleted;
use App\Events\Song\SongCreated;
use App\Events\Song\SongDeleted;
use App\Events\Song\SongDeleting;
use App\Events\Song\SongRestored;
use App\Events\Song\SongUpdated;
use App\Events\Synonym\SynonymCreated;
use App\Events\Synonym\SynonymDeleted;
use App\Events\Synonym\SynonymRestored;
use App\Events\Synonym\SynonymUpdated;
use App\Events\Theme\ThemeCreated;
use App\Events\Theme\ThemeUpdated;
use App\Listeners\UpdateRelatedIndices;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateRelatedIndicesTest extends TestCase
{
    /**
     * UpdateRelatedIndices shall listen to AnimeCreated.
     *
     * @return void
     */
    public function testAnimeCreated()
    {
        Event::fake();

        Event::assertListening(AnimeCreated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to AnimeUpdated.
     *
     * @return void
     */
    public function testAnimeUpdated()
    {
        Event::fake();

        Event::assertListening(AnimeUpdated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to ArtistSongCreated.
     *
     * @return void
     */
    public function testArtistSongCreated()
    {
        Event::fake();

        Event::assertListening(ArtistSongCreated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to ArtistSongDeleted.
     *
     * @return void
     */
    public function testArtistSongDeleted()
    {
        Event::fake();

        Event::assertListening(ArtistSongDeleted::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to ArtistSongUpdated.
     *
     * @return void
     */
    public function testArtistSongUpdated()
    {
        Event::fake();

        Event::assertListening(ArtistSongUpdated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to EntryCreated.
     *
     * @return void
     */
    public function testEntryCreated()
    {
        Event::fake();

        Event::assertListening(EntryCreated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to EntryDeleted.
     *
     * @return void
     */
    public function testEntryDeleted()
    {
        Event::fake();

        Event::assertListening(EntryDeleted::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to EntryDeleting.
     *
     * @return void
     */
    public function testEntryDeleting()
    {
        Event::fake();

        Event::assertListening(EntryDeleting::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to EntryRestored.
     *
     * @return void
     */
    public function testEntryRestored()
    {
        Event::fake();

        Event::assertListening(EntryRestored::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to EntryUpdated.
     *
     * @return void
     */
    public function testEntryUpdated()
    {
        Event::fake();

        Event::assertListening(EntryUpdated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SongCreated.
     *
     * @return void
     */
    public function testSongCreated()
    {
        Event::fake();

        Event::assertListening(SongCreated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SongDeleted.
     *
     * @return void
     */
    public function testSongDeleted()
    {
        Event::fake();

        Event::assertListening(SongDeleted::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SongDeleting.
     *
     * @return void
     */
    public function testSongDeleting()
    {
        Event::fake();

        Event::assertListening(SongDeleting::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SongRestored.
     *
     * @return void
     */
    public function testSongRestored()
    {
        Event::fake();

        Event::assertListening(SongRestored::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SongUpdated.
     *
     * @return void
     */
    public function testSongUpdated()
    {
        Event::fake();

        Event::assertListening(SongUpdated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SynonymCreated.
     *
     * @return void
     */
    public function testSynonymCreated()
    {
        Event::fake();

        Event::assertListening(SynonymCreated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SynonymDeleted.
     *
     * @return void
     */
    public function testSynonymDeleted()
    {
        Event::fake();

        Event::assertListening(SynonymDeleted::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SynonymRestored.
     *
     * @return void
     */
    public function testSynonymRestored()
    {
        Event::fake();

        Event::assertListening(SynonymRestored::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SynonymUpdated.
     *
     * @return void
     */
    public function testSynonymUpdated()
    {
        Event::fake();

        Event::assertListening(SynonymUpdated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to ThemeCreated.
     *
     * @return void
     */
    public function testThemeCreated()
    {
        Event::fake();

        Event::assertListening(ThemeCreated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to ThemeUpdated.
     *
     * @return void
     */
    public function testThemeUpdated()
    {
        Event::fake();

        Event::assertListening(ThemeUpdated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to VideoEntryCreated.
     *
     * @return void
     */
    public function testVideoEntryCreated()
    {
        Event::fake();

        Event::assertListening(VideoEntryCreated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to VideoEntryDeleted.
     *
     * @return void
     */
    public function testVideoEntryDeleted()
    {
        Event::fake();

        Event::assertListening(VideoEntryDeleted::class, UpdateRelatedIndices::class);
    }
}
