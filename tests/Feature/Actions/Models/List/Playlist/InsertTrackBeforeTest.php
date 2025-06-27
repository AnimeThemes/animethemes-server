<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\List\Playlist;

use App\Actions\Models\List\Playlist\InsertTrackBeforeAction;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class InsertTrackBeforeTest.
 */
class InsertTrackBeforeTest extends TestCase
{
    use WithFaker;

    /**
     * The Insert Track Before Action shall set the track as the playlist's first track if inserting before the first track.
     *
     * @return void
     *
     * @throws Exception
     */
    public function test_first_track(): void
    {
        $playlist = Playlist::factory()
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne();

        $first = $playlist->first;

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->createOne();

        $action = new InsertTrackBeforeAction();

        $action->insertBefore($playlist, $track, $first);

        static::assertTrue($playlist->first()->is($track));

        static::assertTrue($first->previous()->is($track));

        static::assertTrue($track->next()->is($first));
        static::assertTrue($track->previous()->doesntExist());
    }

    /**
     * The Insert Track Before Action shall set the track as the last track's previous track if inserting before it.
     *
     * @return void
     *
     * @throws Exception
     */
    public function test_last_track(): void
    {
        $playlist = Playlist::factory()
            ->tracks($this->faker->numberBetween(2, 9))
            ->createOne();

        $last = $playlist->last;

        $previous = $last->previous;

        $track = PlaylistTrack::factory()
            ->for($playlist)
            ->for(Video::factory())
            ->createOne();

        $action = new InsertTrackBeforeAction();

        $action->insertBefore($playlist, $track, $last);

        static::assertTrue($playlist->last()->is($last));

        static::assertTrue($last->previous()->is($track));

        static::assertTrue($track->previous()->is($previous));
        static::assertTrue($track->next()->is($last));
    }
}
