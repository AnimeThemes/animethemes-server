<?php

declare(strict_types=1);

namespace Tests\Feature\Events\List;

use App\Events\List\ExternalProfile\ExternalProfileCreated;
use App\Events\List\ExternalProfile\ExternalProfileDeleted;
use App\Events\List\ExternalProfile\ExternalProfileUpdated;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ExternalProfileTest extends TestCase
{
    /**
     * When a profile is created, a ExternalProfileCreated event shall be dispatched.
     */
    public function testExternalProfileCreatedEventDispatched(): void
    {
        ExternalProfile::factory()->createOne();

        Event::assertDispatched(ExternalProfileCreated::class);
    }

    /**
     * When a profile is deleted, a ExternalProfileDeleted event shall be dispatched.
     */
    public function testExternalProfileDeletedEventDispatched(): void
    {
        $profile = ExternalProfile::factory()->createOne();

        $profile->delete();

        Event::assertDispatched(ExternalProfileDeleted::class);
    }

    /**
     * When a profile is updated, a ExternalProfileUpdated event shall be dispatched.
     */
    public function testExternalProfileUpdatedEventDispatched(): void
    {
        $profile = ExternalProfile::factory()->createOne();
        $changes = ExternalProfile::factory()->makeOne();

        $profile->fill($changes->getAttributes());
        $profile->save();

        Event::assertDispatched(ExternalProfileUpdated::class);
    }

    /**
     * The ExternalProfileUpdated event shall contain embed fields.
     */
    public function testExternalProfileUpdatedEventEmbedFields(): void
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
