<?php

declare(strict_types=1);

use App\Events\List\ExternalProfile\ExternalProfileCreated;
use App\Events\List\ExternalProfile\ExternalProfileDeleted;
use App\Events\List\ExternalProfile\ExternalProfileUpdated;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('external profile created event dispatched', function (): void {
    ExternalProfile::factory()->createOne();

    Event::assertDispatched(ExternalProfileCreated::class);
});

test('external profile deleted event dispatched', function (): void {
    $profile = ExternalProfile::factory()->createOne();

    $profile->delete();

    Event::assertDispatched(ExternalProfileDeleted::class);
});

test('external profile updated event dispatched', function (): void {
    $profile = ExternalProfile::factory()->createOne();
    $changes = ExternalProfile::factory()->makeOne();

    $profile->fill($changes->getAttributes());
    $profile->save();

    Event::assertDispatched(ExternalProfileUpdated::class);
});

test('external profile updated event embed fields', function (): void {
    $profile = ExternalProfile::factory()->createOne();
    $changes = ExternalProfile::factory()->makeOne();

    $profile->fill($changes->getAttributes());
    $profile->save();

    Event::assertDispatched(ExternalProfileUpdated::class, function (ExternalProfileUpdated $event): bool {
        $message = $event->getDiscordMessage();

        return filled(Arr::get($message->embed, 'fields'));
    });
});
