<?php

declare(strict_types=1);

namespace Tests\Feature\Events\List;

use App\Events\List\ExternalProfile\ExternalProfileCreated;
use App\Events\List\ExternalProfile\ExternalProfileDeleted;
use App\Events\List\ExternalProfile\ExternalProfileRestored;
use App\Events\List\ExternalProfile\ExternalProfileUpdated;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class ExternalProfileTest.
 */
class ExternalProfileTest extends TestCase
{
    /**
     * When a profile is created, a ExternalProfileCreated event shall be dispatched.
     *
     * @return void
     */
    public function test_external_profile_created_event_dispatched(): void
    {
        ExternalProfile::factory()->createOne();

        Event::assertDispatched(ExternalProfileCreated::class);
    }

    /**
     * When a profile is deleted, a ExternalProfileDeleted event shall be dispatched.
     *
     * @return void
     */
    public function test_external_profile_deleted_event_dispatched(): void
    {
        $profile = ExternalProfile::factory()->createOne();

        $profile->delete();

        Event::assertDispatched(ExternalProfileDeleted::class);
    }

    /**
     * When a profile is restored, a ExternalProfileRestored event shall be dispatched.
     *
     * @return void
     */
    public function test_external_profile_restored_event_dispatched(): void
    {
        $profile = ExternalProfile::factory()->createOne();

        $profile->restore();

        Event::assertDispatched(ExternalProfileRestored::class);
    }

    /**
     * When a profile is restored, a ExternalProfileUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function test_external_profile_restores_quietly(): void
    {
        $profile = ExternalProfile::factory()->createOne();

        $profile->restore();

        Event::assertNotDispatched(ExternalProfileUpdated::class);
    }

    /**
     * When a profile is updated, a ExternalProfileUpdated event shall be dispatched.
     *
     * @return void
     */
    public function test_external_profile_updated_event_dispatched(): void
    {
        $profile = ExternalProfile::factory()->createOne();
        $changes = ExternalProfile::factory()->makeOne();

        $profile->fill($changes->getAttributes());
        $profile->save();

        Event::assertDispatched(ExternalProfileUpdated::class);
    }

    /**
     * The ExternalProfileUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function test_external_profile_updated_event_embed_fields(): void
    {
        $profile = ExternalProfile::factory()->createOne();
        $changes = ExternalProfile::factory()->makeOne();

        $profile->fill($changes->getAttributes());
        $profile->save();

        Event::assertDispatched(ExternalProfileUpdated::class, function (ExternalProfileUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
