<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Discord;

use App\Models\Discord\DiscordThread;
use App\Models\Wiki\Anime;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class DiscordThreadTest.
 */
class DiscordThreadTest extends TestCase
{
    use WithFaker;

    /**
     * Thread shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $thread = DiscordThread::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($thread->getName());
    }

    /**
     * Thread shall have subtitle.
     *
     * @return void
     */
    public function testHasSubtitle(): void
    {
        $thread = DiscordThread::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($thread->getSubtitle());
    }
}
