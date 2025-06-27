<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\List\Playlist;

use App\Actions\Models\List\Playlist\RemoveTrackAction;
use App\Models\List\Playlist;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class RemoveTrackTest.
 */
class RemoveTrackTest extends TestCase
{
    use WithFaker;

    /**
     * The Remove Track Action shall remove the sole track.
     *
     * @return void
     *
     * @throws Exception
     */
    public function test_remove_sole(): void
    {
        $playlist = Playlist::factory()
            ->tracks(1)
            ->createOne();

        $first = $playlist->first;

        $action = new RemoveTrackAction();

        $action->remove($playlist, $first);

        static::assertTrue($playlist->first()->doesntExist());
        static::assertTrue($playlist->last()->doesntExist());

        static::assertTrue($first->previous()->doesntExist());
        static::assertTrue($first->next()->doesntExist());
    }

    /**
     * The Remove Track Action shall remove the first track and set the second track as first.
     *
     * @return void
     *
     * @throws Exception
     */
    public function test_remove_first(): void
    {
        $playlist = Playlist::factory()
            ->tracks($this->faker->numberBetween(3, 9))
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;

        $action = new RemoveTrackAction();

        $action->remove($playlist, $first);

        static::assertTrue($playlist->first()->is($second));

        static::assertTrue($first->previous()->doesntExist());
        static::assertTrue($first->next()->doesntExist());

        static::assertTrue($second->previous()->doesntExist());
    }

    /**
     * The Remove Track Action shall remove the last track and set the penultimate track as last.
     *
     * @return void
     *
     * @throws Exception
     */
    public function test_remove_last(): void
    {
        $playlist = Playlist::factory()
            ->tracks($this->faker->numberBetween(3, 9))
            ->createOne();

        $last = $playlist->last;
        $previous = $last->previous;

        $action = new RemoveTrackAction();

        $action->remove($playlist, $last);

        static::assertTrue($playlist->last()->is($previous));

        static::assertTrue($last->previous()->doesntExist());
        static::assertTrue($last->next()->doesntExist());

        static::assertTrue($previous->next()->doesntExist());
    }

    /**
     * The Remove Track Action shall remove the second track and fill next and previous relations.
     *
     * @return void
     *
     * @throws Exception
     */
    public function test_remove_second(): void
    {
        $playlist = Playlist::factory()
            ->tracks(3)
            ->createOne();

        $first = $playlist->first;
        $second = $first->next;
        $third = $playlist->last;

        $action = new RemoveTrackAction();

        $action->remove($playlist, $second);

        static::assertTrue($playlist->first()->is($first));
        static::assertTrue($playlist->last()->is($third));

        static::assertTrue($first->previous()->doesntExist());
        static::assertTrue($first->next()->is($third));

        static::assertTrue($second->previous()->doesntExist());
        static::assertTrue($second->next()->doesntExist());

        static::assertTrue($third->previous()->is($first));
        static::assertTrue($third->next()->doesntExist());
    }
}
