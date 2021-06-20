<?php

declare(strict_types=1);

namespace Events\Wiki;

use App\Events\Wiki\Theme\ThemeCreated;
use App\Events\Wiki\Theme\ThemeDeleted;
use App\Events\Wiki\Theme\ThemeRestored;
use App\Events\Wiki\Theme\ThemeUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ThemeTest.
 */
class ThemeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a Theme is created, a ThemeCreated event shall be dispatched.
     *
     * @return void
     */
    public function testThemeCreatedEventDispatched()
    {
        Event::fake(ThemeCreated::class);

        Theme::factory()
            ->for(Anime::factory())
            ->create();

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

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();

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

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();

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

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();
        $changes = Theme::factory()
            ->for(Anime::factory())
            ->create();

        $theme->fill($changes->getAttributes());
        $theme->save();

        Event::assertDispatched(ThemeUpdated::class);
    }
}
