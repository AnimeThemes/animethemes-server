<?php

declare(strict_types=1);

use App\Events\Wiki\Group\GroupCreated;
use App\Events\Wiki\Group\GroupDeleted;
use App\Events\Wiki\Group\GroupRestored;
use App\Events\Wiki\Group\GroupUpdated;
use App\Models\Wiki\Group;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('group created event dispatched', function (): void {
    Group::factory()->createOne();

    Event::assertDispatched(GroupCreated::class);
});

test('group deleted event dispatched', function (): void {
    $group = Group::factory()->createOne();

    $group->delete();

    Event::assertDispatched(GroupDeleted::class);
});

test('group restored event dispatched', function (): void {
    $group = Group::factory()->createOne();

    $group->restore();

    Event::assertDispatched(GroupRestored::class);
});

test('group restores quietly', function (): void {
    $group = Group::factory()->createOne();

    $group->restore();

    Event::assertNotDispatched(GroupUpdated::class);
});

test('group updated event dispatched', function (): void {
    $group = Group::factory()->createOne();
    $changes = Group::factory()->makeOne();

    $group->fill($changes->getAttributes());
    $group->save();

    Event::assertDispatched(GroupUpdated::class);
});

test('group updated event embed fields', function (): void {
    $group = Group::factory()->createOne();
    $changes = Group::factory()->makeOne();

    $group->fill($changes->getAttributes());
    $group->save();

    Event::assertDispatched(GroupUpdated::class, function (GroupUpdated $event): bool {
        $message = $event->getDiscordMessage();

        return filled(Arr::get($message->embed, 'fields'));
    });
});
