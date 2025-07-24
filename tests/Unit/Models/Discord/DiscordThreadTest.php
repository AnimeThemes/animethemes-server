<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Discord;

use App\Models\Discord\DiscordThread;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DiscordThreadTest extends TestCase
{
    use WithFaker;

    /**
     * Thread shall be nameable.
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
     */
    public function testHasSubtitle(): void
    {
        $thread = DiscordThread::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($thread->getSubtitle());
    }

    /**
     * Discord Thread shall belong to an Anime.
     */
    public function testAnime(): void
    {
        $thread = DiscordThread::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $thread->anime());
        static::assertInstanceOf(Anime::class, $thread->anime()->first());
    }
}
