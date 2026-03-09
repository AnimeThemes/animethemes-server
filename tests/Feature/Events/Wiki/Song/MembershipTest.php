<?php

declare(strict_types=1);

use App\Events\Wiki\Song\Membership\MembershipCreated;
use App\Events\Wiki\Song\Membership\MembershipDeleted;
use App\Events\Wiki\Song\Membership\MembershipRestored;
use App\Events\Wiki\Song\Membership\MembershipUpdated;
use App\Models\Wiki\Song\Membership;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('membership created event dispatched', function () {
    Membership::factory()->createOne();

    Event::assertDispatched(MembershipCreated::class);
});

test('membership deleted event dispatched', function () {
    $membership = Membership::factory()->createOne();

    $membership->delete();

    Event::assertDispatched(MembershipDeleted::class);
});

test('membership restored event dispatched', function () {
    $membership = Membership::factory()->createOne();

    $membership->restore();

    Event::assertDispatched(MembershipRestored::class);
});

test('membership restores quietly', function () {
    $membership = Membership::factory()->createOne();

    $membership->restore();

    Event::assertNotDispatched(MembershipUpdated::class);
});

test('membership updated event dispatched', function () {
    $membership = Membership::factory()->createOne();
    $changes = Membership::factory()->makeOne();

    $membership->fill($changes->getAttributes());
    $membership->save();

    Event::assertDispatched(MembershipUpdated::class);
});

test('membership updated event embed fields', function () {
    $membership = Membership::factory()->createOne();
    $changes = Membership::factory()->makeOne();

    $membership->fill($changes->getAttributes());
    $membership->save();

    Event::assertDispatched(MembershipUpdated::class, function (MembershipUpdated $event) {
        $message = $event->getDiscordMessage();

        return ! empty(Arr::get($message->embed, 'fields'));
    });
});
