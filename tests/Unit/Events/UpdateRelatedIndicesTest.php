<?php declare(strict_types=1);

namespace Events;

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

/**
 * Class UpdateRelatedIndicesTest
 * @package Events
 */
class UpdateRelatedIndicesTest extends TestCase
{
    /**
     * UpdateRelatedIndices shall listen to AnimeCreated.
     *
     * @return void
     */
    public function testAnimeCreated()
    {
        $fake = Event::fake();

        $fake->assertListening(AnimeCreated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to AnimeUpdated.
     *
     * @return void
     */
    public function testAnimeUpdated()
    {
        $fake = Event::fake();

        $fake->assertListening(AnimeUpdated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to ArtistSongCreated.
     *
     * @return void
     */
    public function testArtistSongCreated()
    {
        $fake = Event::fake();

        $fake->assertListening(ArtistSongCreated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to ArtistSongDeleted.
     *
     * @return void
     */
    public function testArtistSongDeleted()
    {
        $fake = Event::fake();

        $fake->assertListening(ArtistSongDeleted::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to ArtistSongUpdated.
     *
     * @return void
     */
    public function testArtistSongUpdated()
    {
        $fake = Event::fake();

        $fake->assertListening(ArtistSongUpdated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to EntryCreated.
     *
     * @return void
     */
    public function testEntryCreated()
    {
        $fake = Event::fake();

        $fake->assertListening(EntryCreated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to EntryDeleted.
     *
     * @return void
     */
    public function testEntryDeleted()
    {
        $fake = Event::fake();

        $fake->assertListening(EntryDeleted::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to EntryDeleting.
     *
     * @return void
     */
    public function testEntryDeleting()
    {
        $fake = Event::fake();

        $fake->assertListening(EntryDeleting::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to EntryRestored.
     *
     * @return void
     */
    public function testEntryRestored()
    {
        $fake = Event::fake();

        $fake->assertListening(EntryRestored::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to EntryUpdated.
     *
     * @return void
     */
    public function testEntryUpdated()
    {
        $fake = Event::fake();

        $fake->assertListening(EntryUpdated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SongCreated.
     *
     * @return void
     */
    public function testSongCreated()
    {
        $fake = Event::fake();

        $fake->assertListening(SongCreated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SongDeleted.
     *
     * @return void
     */
    public function testSongDeleted()
    {
        $fake = Event::fake();

        $fake->assertListening(SongDeleted::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SongDeleting.
     *
     * @return void
     */
    public function testSongDeleting()
    {
        $fake = Event::fake();

        $fake->assertListening(SongDeleting::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SongRestored.
     *
     * @return void
     */
    public function testSongRestored()
    {
        $fake = Event::fake();

        $fake->assertListening(SongRestored::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SongUpdated.
     *
     * @return void
     */
    public function testSongUpdated()
    {
        $fake = Event::fake();

        $fake->assertListening(SongUpdated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SynonymCreated.
     *
     * @return void
     */
    public function testSynonymCreated()
    {
        $fake = Event::fake();

        $fake->assertListening(SynonymCreated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SynonymDeleted.
     *
     * @return void
     */
    public function testSynonymDeleted()
    {
        $fake = Event::fake();

        $fake->assertListening(SynonymDeleted::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SynonymRestored.
     *
     * @return void
     */
    public function testSynonymRestored()
    {
        $fake = Event::fake();

        $fake->assertListening(SynonymRestored::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to SynonymUpdated.
     *
     * @return void
     */
    public function testSynonymUpdated()
    {
        $fake = Event::fake();

        $fake->assertListening(SynonymUpdated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to ThemeCreated.
     *
     * @return void
     */
    public function testThemeCreated()
    {
        $fake = Event::fake();

        $fake->assertListening(ThemeCreated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to ThemeUpdated.
     *
     * @return void
     */
    public function testThemeUpdated()
    {
        $fake = Event::fake();

        $fake->assertListening(ThemeUpdated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to VideoEntryCreated.
     *
     * @return void
     */
    public function testVideoEntryCreated()
    {
        $fake = Event::fake();

        $fake->assertListening(VideoEntryCreated::class, UpdateRelatedIndices::class);
    }

    /**
     * UpdateRelatedIndices shall listen to VideoEntryDeleted.
     *
     * @return void
     */
    public function testVideoEntryDeleted()
    {
        $fake = Event::fake();

        $fake->assertListening(VideoEntryDeleted::class, UpdateRelatedIndices::class);
    }
}
