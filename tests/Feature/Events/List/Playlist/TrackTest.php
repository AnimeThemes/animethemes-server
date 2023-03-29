<?php

declare(strict_types=1);

namespace Tests\Feature\Events\List\Playlist;

use App\Contracts\Models\HasHashids;
use App\Events\List\Playlist\Track\TrackCreated;
use App\Events\List\Playlist\Track\TrackDeleted;
use App\Events\List\Playlist\Track\TrackRestored;
use App\Events\List\Playlist\Track\TrackUpdated;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class TrackTest.
 */
class TrackTest extends TestCase
{
    /**
     * When a Playlist Track is created, a TrackCreated event shall be dispatched.
     *
     * @return void
     */
    public function testTrackCreatedEventDispatched(): void
    {
        Event::fake();

        PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        Event::assertDispatched(TrackCreated::class);
    }

    /**
     * When a Playlist Track is deleted, a TrackDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testTrackDeletedEventDispatched(): void
    {
        Event::fake();

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $track->delete();

        Event::assertDispatched(TrackDeleted::class);
    }

    /**
     * When a Playlist Track is restored, a TrackRestored event shall be dispatched.
     *
     * @return void
     */
    public function testTrackRestoredEventDispatched(): void
    {
        Event::fake();

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $track->restore();

        Event::assertDispatched(TrackRestored::class);
    }

    /**
     * When a Track is restored, a TrackUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testTrackRestoresQuietly(): void
    {
        Event::fake();

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $track->restore();

        Event::assertNotDispatched(TrackUpdated::class);
    }

    /**
     * When a Track is updated, a TrackUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testTrackUpdatedEventDispatched(): void
    {
        Event::fake();

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $changes = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->makeOne();

        $track->fill($changes->getAttributes());
        $track->save();

        Event::assertDispatched(TrackUpdated::class);
    }

    /**
     * The TrackUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function testPlaylistUpdatedEventEmbedFields(): void
    {
        Event::fake();

        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        $changes = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->makeOne();

        $track->fill($changes->getAttributes());
        $track->save();

        Event::assertDispatched(TrackUpdated::class, function (TrackUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }

    /**
     * The Track Created event shall assign hashids to the track.
     *
     * @return void
     */
    public function testPlaylistCreatedAssignsHashids(): void
    {
        Event::fakeExcept(TrackCreated::class);

        PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        static::assertDatabaseMissing(PlaylistTrack::class, [HasHashids::ATTRIBUTE_HASHID => null]);
    }
}
