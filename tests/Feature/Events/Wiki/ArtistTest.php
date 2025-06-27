<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Artist\ArtistCreated;
use App\Events\Wiki\Artist\ArtistDeleted;
use App\Events\Wiki\Artist\ArtistRestored;
use App\Events\Wiki\Artist\ArtistUpdated;
use App\Models\Wiki\Artist;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ArtistTest.
 */
class ArtistTest extends TestCase
{
    /**
     * When an Artist is created, an ArtistCreated event shall be dispatched.
     *
     * @return void
     */
    public function test_artist_created_event_dispatched(): void
    {
        Artist::factory()->createOne();

        Event::assertDispatched(ArtistCreated::class);
    }

    /**
     * When an Artist is deleted, an ArtistDeleted event shall be dispatched.
     *
     * @return void
     */
    public function test_artist_deleted_event_dispatched(): void
    {
        $artist = Artist::factory()->createOne();

        $artist->delete();

        Event::assertDispatched(ArtistDeleted::class);
    }

    /**
     * When an Artist is restored, an ArtistRestored event shall be dispatched.
     *
     * @return void
     */
    public function test_artist_restored_event_dispatched(): void
    {
        $artist = Artist::factory()->createOne();

        $artist->restore();

        Event::assertDispatched(ArtistRestored::class);
    }

    /**
     * When an Artist is restored, an ArtistUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function test_artist_restores_quietly(): void
    {
        $artist = Artist::factory()->createOne();

        $artist->restore();

        Event::assertNotDispatched(ArtistUpdated::class);
    }

    /**
     * When an Artist is updated, an ArtistUpdated event shall be dispatched.
     *
     * @return void
     */
    public function test_artist_updated_event_dispatched(): void
    {
        $artist = Artist::factory()->createOne();
        $changes = Artist::factory()->makeOne();

        $artist->fill($changes->getAttributes());
        $artist->save();

        Event::assertDispatched(ArtistUpdated::class);
    }

    /**
     * The ArtistUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function test_artist_updated_event_embed_fields(): void
    {
        $artist = Artist::factory()->createOne();
        $changes = Artist::factory()->makeOne();

        $artist->fill($changes->getAttributes());
        $artist->save();

        Event::assertDispatched(ArtistUpdated::class, function (ArtistUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
