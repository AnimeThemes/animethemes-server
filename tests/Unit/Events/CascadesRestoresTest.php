<?php declare(strict_types=1);

namespace Events;

use App\Events\Anime\AnimeRestored;
use App\Events\Theme\ThemeRestored;
use App\Listeners\CascadesRestores;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class CascadesRestoresTest
 * @package Events
 */
class CascadesRestoresTest extends TestCase
{
    /**
     * CascadesRestores shall listen to AnimeRestored.
     *
     * @return void
     */
    public function testAnimeRestored()
    {
        $fake = Event::fake();

        $fake->assertListening(AnimeRestored::class, CascadesRestores::class);
    }

    /**
     * CascadesRestores shall listen to ThemeRestored.
     *
     * @return void
     */
    public function testThemeRestored()
    {
        $fake = Event::fake();

        $fake->assertListening(ThemeRestored::class, CascadesRestores::class);
    }
}
