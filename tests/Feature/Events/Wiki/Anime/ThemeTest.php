<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki\Anime;

use App\Events\Wiki\Anime\Theme\ThemeCreated;
use App\Events\Wiki\Anime\Theme\ThemeDeleted;
use App\Events\Wiki\Anime\Theme\ThemeRestored;
use App\Events\Wiki\Anime\Theme\ThemeUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ThemeTest extends TestCase
{
    /**
     * When a Theme is created, a ThemeCreated event shall be dispatched.
     */
    public function testThemeCreatedEventDispatched(): void
    {
        AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        Event::assertDispatched(ThemeCreated::class);
    }

    /**
     * When a Theme is deleted, a ThemeDeleted event shall be dispatched.
     */
    public function testThemeDeletedEventDispatched(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        $theme->delete();

        Event::assertDispatched(ThemeDeleted::class);
    }

    /**
     * When a Theme is restored, a ThemeRestored event shall be dispatched.
     */
    public function testThemeRestoredEventDispatched(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        $theme->restore();

        Event::assertDispatched(ThemeRestored::class);
    }

    /**
     * When a Theme is restored, a ThemeUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     */
    public function testThemeRestoresQuietly(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        $theme->restore();

        Event::assertNotDispatched(ThemeUpdated::class);
    }

    /**
     * When a Theme is updated, a ThemeUpdated event shall be dispatched.
     */
    public function testThemeUpdatedEventDispatched(): void
    {
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

    /**
     * The ThemeUpdated event shall contain embed fields.
     */
    public function testThemeUpdatedEventEmbedFields(): void
    {
        $theme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->createOne();

        $changes = AnimeTheme::factory()
            ->for(Anime::factory())
            ->makeOne();

        $theme->fill($changes->getAttributes());
        $theme->save();

        Event::assertDispatched(ThemeUpdated::class, function (ThemeUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
