<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Pivot\Wiki;

use App\Events\Pivot\Wiki\SongResource\SongResourceCreated;
use App\Events\Pivot\Wiki\SongResource\SongResourceDeleted;
use App\Events\Pivot\Wiki\SongResource\SongResourceUpdated;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\SongResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SongResourceTest extends TestCase
{
    /**
     * When an Song is attached to a Resource or vice versa, an SongResourceCreated event shall be dispatched.
     */
    public function testSongResourceCreatedEventDispatched(): void
    {
        $song = Song::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $song->resources()->attach($resource);

        Event::assertDispatched(SongResourceCreated::class);
    }

    /**
     * When an Song is detached from a Resource or vice versa, an SongResourceDeleted event shall be dispatched.
     */
    public function testSongResourceDeletedEventDispatched(): void
    {
        $song = Song::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $song->resources()->attach($resource);
        $song->resources()->detach($resource);

        Event::assertDispatched(SongResourceDeleted::class);
    }

    /**
     * When an Song Resource pivot is updated, an SongResourceUpdated event shall be dispatched.
     */
    public function testSongResourceUpdatedEventDispatched(): void
    {
        $song = Song::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $songResource = SongResource::factory()
            ->for($song, 'song')
            ->for($resource, 'resource')
            ->createOne();

        $changes = SongResource::factory()
            ->for($song, 'song')
            ->for($resource, 'resource')
            ->makeOne();

        $songResource->fill($changes->getAttributes());
        $songResource->save();

        Event::assertDispatched(SongResourceUpdated::class);
    }

    /**
     * The SongResourceUpdated event shall contain embed fields.
     */
    public function testSongResourceUpdatedEventEmbedFields(): void
    {
        $song = Song::factory()->createOne();
        $resource = ExternalResource::factory()->createOne();

        $songResource = SongResource::factory()
            ->for($song, 'song')
            ->for($resource, 'resource')
            ->createOne();

        $changes = SongResource::factory()
            ->for($song, 'song')
            ->for($resource, 'resource')
            ->makeOne();

        $songResource->fill($changes->getAttributes());
        $songResource->save();

        Event::assertDispatched(SongResourceUpdated::class, function (SongResourceUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
