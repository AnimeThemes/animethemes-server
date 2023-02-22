<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\List\Playlist;

use App\Actions\Models\List\Playlist\InsertTrackAfterAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class InsertTrackAfterTest.
 */
class InsertTrackAfterTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Insert Track After Action shall set the track as the playlist's last track if inserting after the last track.
     *
     * @return void
     */
    public function testLastTrack(): void
    {
        $playlist = Playlist::factory()
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne();

        $last = $playlist->last;

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->createOne();

        $action = new InsertTrackAfterAction();

        $action->insertAfter($playlist, $track, $last);

        static::assertTrue($playlist->last()->is($track));

        static::assertTrue($last->next()->is($track));

        static::assertTrue($track->previous()->is($last));
        static::assertTrue($track->next()->doesntExist());
    }

    /**
     * The Insert Track After Action shall set the track as the first track's next track if inserting after it.
     *
     * @return void
     */
    public function testFirstTrack(): void
    {
        $playlist = Playlist::factory()
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne();

        $first = $playlist->first;

        $next = $first->next;

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->createOne();

        $action = new InsertTrackAfterAction();

        $action->insertAfter($playlist, $track, $first);

        static::assertTrue($playlist->first()->is($first));

        static::assertTrue($first->next()->is($track));

        static::assertTrue($track->previous()->is($first));
        static::assertTrue($track->next()->is($next));
    }
}
