<?php

declare(strict_types=1);

namespace Tests\Unit\Models\List\Playlist;

use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

/**
 * Class TrackTest.
 */
class TrackTest extends TestCase
{
    /**
     * Anime shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        static::assertIsString($track->getName());
    }

    /**
     * Playlists shall include playlist and track ids for hashids encoding.
     *
     * @return void
     */
    public function testHashids(): void
    {
        $playlist = Playlist::factory()->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        static::assertEmpty(array_diff([$playlist->playlist_id, $track->track_id], $track->hashids()));
        static::assertEmpty(array_diff($track->hashids(), [$playlist->playlist_id, $track->track_id]));
    }

    /**
     * Playlist Tracks shall belong to a Playlist.
     *
     * @return void
     */
    public function testPlaylist(): void
    {
        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $track->playlist());
        static::assertInstanceOf(Playlist::class, $track->playlist()->first());
    }

    /**
     * Playlist Tracks shall link to a previous Track.
     *
     * @return void
     */
    public function testPrevious(): void
    {
        $playlist = Playlist::factory()->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $previous = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $track->fill([
            PlaylistTrack::ATTRIBUTE_PREVIOUS => $previous->getKey(),
        ]);

        $track->save();

        static::assertInstanceOf(BelongsTo::class, $track->previous());
        static::assertInstanceOf(PlaylistTrack::class, $track->previous()->first());
    }

    /**
     * Playlist Tracks shall link to a next Track.
     *
     * @return void
     */
    public function testNext(): void
    {
        $playlist = Playlist::factory()->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $next = PlaylistTrack::factory()
            ->for($playlist)
            ->createOne();

        $track->fill([
            PlaylistTrack::ATTRIBUTE_NEXT => $next->getKey(),
        ]);

        $track->save();

        static::assertInstanceOf(BelongsTo::class, $track->next());
        static::assertInstanceOf(PlaylistTrack::class, $track->next()->first());
    }

    /**
     * Playlist Tracks shall belong to a Video.
     *
     * @return void
     */
    public function testVideo(): void
    {
        $track = PlaylistTrack::factory()
            ->for(Playlist::factory())
            ->for(Video::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $track->video());
        static::assertInstanceOf(Video::class, $track->video()->first());
    }
}
