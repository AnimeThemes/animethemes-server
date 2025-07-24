<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Wiki;

use App\Events\Wiki\Group\GroupCreated;
use App\Events\Wiki\Group\GroupDeleted;
use App\Events\Wiki\Group\GroupRestored;
use App\Events\Wiki\Group\GroupUpdated;
use App\Models\Wiki\Group;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class GroupTest extends TestCase
{
    /**
     * When a Group is created, a GroupCreated event shall be dispatched.
     */
    public function testGroupCreatedEventDispatched(): void
    {
        Group::factory()->createOne();

        Event::assertDispatched(GroupCreated::class);
    }

    /**
     * When a Group is deleted, a GroupDeleted event shall be dispatched.
     */
    public function testGroupDeletedEventDispatched(): void
    {
        $group = Group::factory()->createOne();

        $group->delete();

        Event::assertDispatched(GroupDeleted::class);
    }

    /**
     * When a Group is restored, a GroupRestored event shall be dispatched.
     */
    public function testGroupRestoredEventDispatched(): void
    {
        $group = Group::factory()->createOne();

        $group->restore();

        Event::assertDispatched(GroupRestored::class);
    }

    /**
     * When a Group is restored, a GroupUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     */
    public function testGroupRestoresQuietly(): void
    {
        $group = Group::factory()->createOne();

        $group->restore();

        Event::assertNotDispatched(GroupUpdated::class);
    }

    /**
     * When a Group is updated, a GroupUpdated event shall be dispatched.
     */
    public function testGroupUpdatedEventDispatched(): void
    {
        $group = Group::factory()->createOne();
        $changes = Group::factory()->makeOne();

        $group->fill($changes->getAttributes());
        $group->save();

        Event::assertDispatched(GroupUpdated::class);
    }

    /**
     * The GroupUpdated event shall contain embed fields.
     */
    public function testGroupUpdatedEventEmbedFields(): void
    {
        $group = Group::factory()->createOne();
        $changes = Group::factory()->makeOne();

        $group->fill($changes->getAttributes());
        $group->save();

        Event::assertDispatched(GroupUpdated::class, function (GroupUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
