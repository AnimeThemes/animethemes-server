<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Admin;

use App\Events\Admin\Feature\FeatureCreated;
use App\Events\Admin\Feature\FeatureDeleted;
use App\Events\Admin\Feature\FeatureUpdated;
use App\Models\Admin\Feature;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class FeatureTest.
 */
class FeatureTest extends TestCase
{
    /**
     * When a Feature is created, a FeatureCreated event shall be dispatched.
     *
     * @return void
     */
    public function testFeatureCreatedEventDispatched(): void
    {
        Feature::factory()->create();

        Event::assertDispatched(FeatureCreated::class);
    }

    /**
     * When a Feature is deleted, a FeatureDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testFeatureDeletedEventDispatched(): void
    {
        $feature = Feature::factory()->create();

        $feature->delete();

        Event::assertDispatched(FeatureDeleted::class);
    }

    /**
     * When a Feature is updated, a FeatureUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testFeatureUpdatedEventDispatched(): void
    {
        $feature = Feature::factory()->createOne();

        $feature->update([
            Feature::ATTRIBUTE_VALUE => ! $feature->value,
        ]);

        Event::assertDispatched(FeatureUpdated::class);
    }

    /**
     * The FeatureUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function testFeatureUpdatedEventEmbedFields(): void
    {
        $feature = Feature::factory()->createOne();

        $feature->update([
            Feature::ATTRIBUTE_VALUE => ! $feature->value,
        ]);

        Event::assertDispatched(FeatureUpdated::class, function (FeatureUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
