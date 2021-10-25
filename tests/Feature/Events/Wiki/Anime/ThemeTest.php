<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki\Anime;

use App\Events\Wiki\Anime\Theme\ThemeCreated;
use App\Events\Wiki\Anime\Theme\ThemeDeleted;
use App\Events\Wiki\Anime\Theme\ThemeRestored;
use App\Events\Wiki\Anime\Theme\ThemeUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ThemeTest.
 */
class ThemeTest extends TestCase
{
    /**
     * When a Theme is created, a ThemeCreated event shall be dispatched.
     *
     * @return void
     */
    public function testThemeCreatedEventDispatched()
    {
        Event::fake(ThemeCreated::class);

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        Event::assertDispatched(ThemeCreated::class);
    }

    /**
     * When a Theme is deleted, a ThemeDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testThemeDeletedEventDispatched()
    {
        Event::fake(ThemeDeleted::class);

        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        $theme->delete();

        Event::assertDispatched(ThemeDeleted::class);
    }

    /**
     * When a Theme is restored, a ThemeRestored event shall be dispatched.
     *
     * @return void
     */
    public function testThemeRestoredEventDispatched()
    {
        Event::fake(ThemeRestored::class);

        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        $theme->restore();

        Event::assertDispatched(ThemeRestored::class);
    }

    /**
     * When a Theme is updated, a ThemeUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testThemeUpdatedEventDispatched()
    {
        Event::fake(ThemeUpdated::class);

        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        $changes = AnimeTheme::factory()
            ->for(Anime::factory())
            ->makeOne();

        $theme->fill($changes->getAttributes());
        $theme->save();

        Event::assertDispatched(ThemeUpdated::class);
    }
}
