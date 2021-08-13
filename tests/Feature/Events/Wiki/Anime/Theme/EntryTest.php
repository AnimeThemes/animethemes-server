<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki\Anime\Theme;

use App\Events\Wiki\Anime\Theme\Entry\EntryCreated;
use App\Events\Wiki\Anime\Theme\Entry\EntryDeleted;
use App\Events\Wiki\Anime\Theme\Entry\EntryRestored;
use App\Events\Wiki\Anime\Theme\Entry\EntryUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\Theme;
use App\Models\Wiki\Anime\Theme\Entry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class EntryTest.
 */
class EntryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an Entry is created, an EntryCreated event shall be dispatched.
     *
     * @return void
     */
    public function testEntryCreatedEventDispatched()
    {
        Event::fake(EntryCreated::class);

        Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
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
        Event::fake(EntryDeleted::class);

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
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
        Event::fake(EntryRestored::class);

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->createOne();

        $entry->restore();

        Event::assertDispatched(EntryRestored::class);
    }

    /**
     * When an Entry is updated, an EntryUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testEntryUpdatedEventDispatched()
    {
        Event::fake(EntryUpdated::class);

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->createOne();

        $changes = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->makeOne();

        $entry->fill($changes->getAttributes());
        $entry->save();

        Event::assertDispatched(EntryUpdated::class);
    }
}
