<?php

namespace Tests\Unit\Events;

use App\Events\Anime\AnimeDeleting;
use App\Events\Theme\ThemeDeleting;
use App\Listeners\CascadesDeletes;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CascadesDeletesTest extends TestCase
{
    /**
     * CascadesDeletes shall listen to AnimeDeleting.
     *
     * @return void
     */
    public function testAnimeDeleting()
    {
        Event::fake();

        Event::assertListening(AnimeDeleting::class, CascadesDeletes::class);
    }

    /**
     * CascadesDeletes shall listen to ThemeDeleting.
     *
     * @return void
     */
    public function testThemeDeleting()
    {
        Event::fake();

        Event::assertListening(ThemeDeleting::class, CascadesDeletes::class);
    }
}
