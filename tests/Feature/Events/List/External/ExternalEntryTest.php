<?php

declare(strict_types=1);

namespace Tests\Feature\Events\List\External;

use App\Events\List\ExternalProfile\ExternalEntry\ExternalEntryCreated;
use App\Events\List\ExternalProfile\ExternalEntry\ExternalEntryDeleted;
use App\Events\List\ExternalProfile\ExternalEntry\ExternalEntryRestored;
use App\Events\List\ExternalProfile\ExternalEntry\ExternalEntryUpdated;
use App\Models\List\ExternalProfile;
use App\Models\List\External\ExternalEntry;
use App\Models\Wiki\Anime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ExternalEntryTest.
 */
class ExternalEntryTest extends TestCase
{
    /**
     * When an External Entry is created, an ExternalEntryCreated event shall be dispatched.
     *
     * @return void
     */
    public function testExternalEntryCreatedEventDispatched(): void
    {
        ExternalEntry::factory()
            ->for(ExternalProfile::factory())
            ->for(Anime::factory())
            ->createOne();

        Event::assertDispatched(ExternalEntryCreated::class);
    }

    /**
     * When an External Entry is deleted, an ExternalEntryDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testExternalEntryDeletedEventDispatched(): void
    {
        $entry = ExternalEntry::factory()
            ->for(ExternalProfile::factory())
            ->for(Anime::factory())
            ->createOne();

        $entry->delete();

        Event::assertDispatched(ExternalEntryDeleted::class);
    }

    /**
     * When an External Entry is restored, an ExternalEntryRestored event shall be dispatched.
     *
     * @return void
     */
    public function testExternalEntryRestoredEventDispatched(): void
    {
        $entry = ExternalEntry::factory()
            ->for(ExternalProfile::factory())
            ->for(Anime::factory())
            ->createOne();

        $entry->restore();

        Event::assertDispatched(ExternalEntryRestored::class);
    }

    /**
     * When an External Entry is restored, a ExternalEntryUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function testExternalEntryRestoresQuietly(): void
    {
        $entry = ExternalEntry::factory()
            ->for(ExternalProfile::factory())
            ->for(Anime::factory())
            ->createOne();

        $entry->restore();

        Event::assertNotDispatched(ExternalEntryUpdated::class);
    }

    /**
     * When an External Entry is updated, a ExternalEntryUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testExternalEntryUpdatedEventDispatched(): void
    {
        $entry = ExternalEntry::factory()
            ->for(ExternalProfile::factory())
            ->for(Anime::factory())
            ->createOne();

        $changes = ExternalEntry::factory()
            ->for(ExternalProfile::factory())
            ->for(Anime::factory())
            ->makeOne();

        $entry->fill($changes->getAttributes());
        $entry->save();

        Event::assertDispatched(ExternalEntryUpdated::class);
    }

    /**
     * The ExternalEntryUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function testExternalEntryUpdatedEventEmbedFields(): void
    {
        $entry = ExternalEntry::factory()
            ->for(ExternalProfile::factory())
            ->for(Anime::factory())
            ->createOne();

        $changes = ExternalEntry::factory()
            ->for(ExternalProfile::factory())
            ->for(Anime::factory())
            ->makeOne();

        $entry->fill($changes->getAttributes());
        $entry->save();

        Event::assertDispatched(ExternalEntryUpdated::class, function (ExternalEntryUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
