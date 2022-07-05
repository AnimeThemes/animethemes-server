<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Song\SongCreated;
use App\Events\Wiki\Song\SongDeleted;
use App\Events\Wiki\Song\SongRestored;
use App\Events\Wiki\Song\SongUpdated;
use App\Models\Wiki\Song;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class SongTest.
 */
class SongTest extends TestCase
{
    /**
     * When a Song is created, a SongCreated event shall be dispatched.
     *
     * @return void
     */
    public function testSongCreatedEventDispatched(): void
    {
        Event::fake();

        Song::factory()->createOne();

        Event::assertDispatched(SongCreated::class);
    }

    /**
     * When a Song is deleted, a SongDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testSongDeletedEventDispatched(): void
    {
        Event::fake();

        $song = Song::factory()->createOne();

        $song->delete();

        Event::assertDispatched(SongDeleted::class);
    }

    /**
     * When a Song is restored, a SongRestored event shall be dispatched.
     *
     * @return void
     */
    public function testSongRestoredEventDispatched(): void
    {
        Event::fake();

        $song = Song::factory()->createOne();

        $song->restore();

        Event::assertDispatched(SongRestored::class);
    }

    /**
     * When a Song is restored, a SongUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testSongRestoresQuietly(): void
    {
        Event::fake();

        $song = Song::factory()->createOne();

        $song->restore();

        Event::assertNotDispatched(SongUpdated::class);
    }

    /**
     * When a Song is updated, a SongUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testSongUpdatedEventDispatched(): void
    {
        Event::fake();

        $song = Song::factory()->createOne();
        $changes = Song::factory()->makeOne();

        $song->fill($changes->getAttributes());
        $song->save();

        Event::assertDispatched(SongUpdated::class);
    }

    /**
     * The SongUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function testSongUpdatedEventEmbedFields(): void
    {
        Event::fake();

        $anime = Song::factory()->createOne();
        $changes = Song::factory()->makeOne();

        $anime->fill($changes->getAttributes());
        $anime->save();

        Event::assertDispatched(SongUpdated::class, function (SongUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
