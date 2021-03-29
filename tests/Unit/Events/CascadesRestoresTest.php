<?php

namespace Tests\Unit\Events;

use App\Events\Anime\AnimeRestored;
use App\Events\Theme\ThemeRestored;
use App\Listeners\CascadesRestores;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CascadesRestoresTest extends TestCase
{
    /**
     * CascadesRestores shall listen to AnimeRestored.
     *
     * @return void
     */
    public function testAnimeRestored()
    {
        Event::fake();

        Event::assertListening(AnimeRestored::class, CascadesRestores::class);
    }

    /**
     * CascadesRestores shall listen to ThemeRestored.
     *
     * @return void
     */
    public function testThemeRestored()
    {
        Event::fake();

        Event::assertListening(ThemeRestored::class, CascadesRestores::class);
    }
}
