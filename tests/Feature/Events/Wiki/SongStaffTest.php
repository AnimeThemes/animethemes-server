<?php

declare(strict_types=1);

use App\Events\Wiki\SongStaff\SongStaffCreated;
use App\Events\Wiki\SongStaff\SongStaffDeleted;
use App\Events\Wiki\SongStaff\SongStaffRestored;
use App\Events\Wiki\SongStaff\SongStaffUpdated;
use App\Models\Wiki\SongStaff;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

test('song staff created event dispatched', function (): void {
    SongStaff::factory()->createOne();

    Event::assertDispatched(SongStaffCreated::class);
});

test('song staff deleted event dispatched', function (): void {
    $staff = SongStaff::factory()->createOne();

    $staff->delete();

    Event::assertDispatched(SongStaffDeleted::class);
});

test('song staff restored event dispatched', function (): void {
    $staff = SongStaff::factory()->createOne();

    $staff->restore();

    Event::assertDispatched(SongStaffRestored::class);
});

test('song staff restores quietly', function (): void {
    $staff = SongStaff::factory()->createOne();

    $staff->restore();

    Event::assertNotDispatched(SongStaffUpdated::class);
});

test('song staff updated event dispatched', function (): void {
    $staff = SongStaff::factory()->createOne();
    $changes = SongStaff::factory()->makeOne();

    $staff->fill($changes->getAttributes());
    $staff->save();

    Event::assertDispatched(SongStaffUpdated::class);
});

test('song staff updated event embed fields', function (): void {
    $staff = SongStaff::factory()->createOne();
    $changes = SongStaff::factory()->makeOne();

    $staff->fill($changes->getAttributes());
    $staff->save();

    Event::assertDispatched(SongStaffUpdated::class, function (SongStaffUpdated $event): bool {
        $message = $event->getDiscordMessage();

        return filled(Arr::get($message->embed, 'fields'));
    });
});
