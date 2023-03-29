<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\List\Playlist;

use App\Actions\Models\List\Playlist\InsertTrackAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Exception;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class InsertTrackTest.
 */
class InsertTrackTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Insert Track Action shall set the first inserted track as first and last.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFirstTrack(): void
    {
        $playlist = Playlist::factory()->createOne();

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->createOne();

        $action = new InsertTrackAction();

        $action->insert($playlist, $track);

        static::assertTrue($playlist->first()->is($track));
        static::assertTrue($playlist->last()->is($track));
    }

    /**
     * The Insert Track Action shall set the second track as the first's next track and the playlist's last track.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testSecondTrack(): void
    {
        $playlist = Playlist::factory()->createOne();

        $first = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->createOne();

        $second = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->createOne();

        $action = new InsertTrackAction();

        $action->insert($playlist, $first);
        $action->insert($playlist, $second);

        static::assertTrue($playlist->first()->is($first));
        static::assertTrue($playlist->last()->is($second));

        static::assertTrue($first->previous()->doesntExist());
        static::assertTrue($first->next()->is($second));

        static::assertTrue($second->previous()->is($first));
        static::assertTrue($second->next()->doesntExist());
    }

    /**
     * The Insert Track Action shall set the third track as the second's next track and the playlist's last track.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testThirdTrack(): void
    {
        $playlist = Playlist::factory()->createOne();

        $first = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->createOne();

        $second = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->createOne();

        $third = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->createOne();

        $action = new InsertTrackAction();

        $action->insert($playlist, $first);
        $action->insert($playlist, $second);
        $action->insert($playlist, $third);

        static::assertTrue($playlist->first()->is($first));
        static::assertTrue($playlist->last()->is($third));

        static::assertTrue($first->previous()->doesntExist());
        static::assertTrue($first->next()->is($second));

        static::assertTrue($second->previous()->is($first));
        static::assertTrue($second->next()->is($third));

        static::assertTrue($third->previous()->is($second));
        static::assertTrue($third->next()->doesntExist());
    }
}
