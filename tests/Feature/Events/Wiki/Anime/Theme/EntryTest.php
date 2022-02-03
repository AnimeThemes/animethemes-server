<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki\Anime\Theme;

use App\Events\Wiki\Anime\Theme\Entry\EntryCreated;
use App\Events\Wiki\Anime\Theme\Entry\EntryDeleted;
use App\Events\Wiki\Anime\Theme\Entry\EntryRestored;
use App\Events\Wiki\Anime\Theme\Entry\EntryUpdated;
use App\Events\Wiki\Anime\Theme\ThemeCreating;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class EntryTest.
 */
class EntryTest extends TestCase
{
    /**
     * When an Entry is created, an EntryCreated event shall be dispatched.
     *
     * @return void
     */
    public function testEntryCreatedEventDispatched()
    {
        Event::fakeExcept(ThemeCreating::class);

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        Event::assertDispatched(EntryCreated::class);
    }

    /**
     * When an Entry is deleted, an EntryDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testEntryDeletedEventDispatched()
    {
        Event::fakeExcept(ThemeCreating::class);

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->delete();

        Event::assertDispatched(EntryDeleted::class);
    }

    /**
     * When an Entry is restored, an EntryRestored event shall be dispatched.
     *
     * @return void
     */
    public function testEntryRestoredEventDispatched()
    {
        Event::fakeExcept(ThemeCreating::class);

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->restore();

        Event::assertDispatched(EntryRestored::class);
    }

    /**
     * When an Entry is restored, an EntryUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testEntryRestoresQuietly()
    {
        Event::fakeExcept(ThemeCreating::class);

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->restore();

        Event::assertNotDispatched(EntryUpdated::class);
    }

    /**
     * When an Entry is updated, an EntryUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testEntryUpdatedEventDispatched()
    {
        Event::fakeExcept(ThemeCreating::class);

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->createOne();

        $changes = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->makeOne();

        $entry->fill($changes->getAttributes());
        $entry->save();

        Event::assertDispatched(EntryUpdated::class);
    }
}
