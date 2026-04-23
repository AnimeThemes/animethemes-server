<?php

declare(strict_types=1);

use App\Events\Admin\Feature\FeatureCreated;
use App\Events\Admin\Feature\FeatureDeleted;
use App\Events\Admin\Feature\FeatureUpdated;
use App\Models\Admin\Feature;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('feature created event dispatched', function (): void {
    Feature::factory()->create();

    Event::assertDispatched(FeatureCreated::class);
});

test('feature deleted event dispatched', function (): void {
    $feature = Feature::factory()->create();

    $feature->delete();

    Event::assertDispatched(FeatureDeleted::class);
});

test('feature updated event dispatched', function (): void {
    $feature = Feature::factory()->createOne();

    $feature->update([
        Feature::ATTRIBUTE_VALUE => ! $feature->value,
    ]);

    Event::assertDispatched(FeatureUpdated::class);
});

test('feature updated event embed fields', function (): void {
    $feature = Feature::factory()->createOne();

    $feature->update([
        Feature::ATTRIBUTE_VALUE => ! $feature->value,
    ]);

    Event::assertDispatched(FeatureUpdated::class, function (FeatureUpdated $event): bool {
        $message = $event->getDiscordMessage();

        return filled(Arr::get($message->embed, 'fields'));
    });
});
