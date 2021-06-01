<?php declare(strict_types=1);

namespace Events;

use App\Events\Anime\AnimeDeleting;
use App\Events\Theme\ThemeDeleting;
use App\Listeners\CascadesDeletes;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class CascadesDeletesTest
 * @package Events
 */
class CascadesDeletesTest extends TestCase
{
    /**
     * CascadesDeletes shall listen to AnimeDeleting.
     *
     * @return void
     */
    public function testAnimeDeleting()
    {
        $fake = Event::fake();

        $fake->assertListening(AnimeDeleting::class, CascadesDeletes::class);
    }

    /**
     * CascadesDeletes shall listen to ThemeDeleting.
     *
     * @return void
     */
    public function testThemeDeleting()
    {
        $fake = Event::fake();

        $fake->assertListening(ThemeDeleting::class, CascadesDeletes::class);
    }
}
