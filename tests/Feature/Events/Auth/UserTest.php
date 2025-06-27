<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Auth;

use App\Events\Auth\User\UserCreated;
use App\Events\Auth\User\UserDeleted;
use App\Events\Auth\User\UserRestored;
use App\Events\Auth\User\UserUpdated;
use App\Models\Auth\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class UserTest.
 */
class UserTest extends TestCase
{
    /**
     * When a User is created, a UserCreated event shall be dispatched.
     *
     * @return void
     */
    public function test_user_created_event_dispatched(): void
    {
        User::factory()->createOne();

        Event::assertDispatched(UserCreated::class);
    }

    /**
     * When a User is deleted, a UserDeleted event shall be dispatched.
     *
     * @return void
     */
    public function test_user_deleted_event_dispatched(): void
    {
        $user = User::factory()->createOne();

        $user->delete();

        Event::assertDispatched(UserDeleted::class);
    }

    /**
     * When a User is restored, a UserRestored event shall be dispatched.
     *
     * @return void
     */
    public function test_user_restored_event_dispatched(): void
    {
        $user = User::factory()->createOne();

        $user->restore();

        Event::assertDispatched(UserRestored::class);
    }

    /**
     * When a User is restored, a UserUpdated event shall not be dispatched.
     * Note: This is a customization that overrides default framework behavior.
     * An updated event is fired on restore.
     *
     * @return void
     */
    public function test_user_restores_quietly(): void
    {
        $user = User::factory()->createOne();

        $user->restore();

        Event::assertNotDispatched(UserUpdated::class);
    }

    /**
     * When a User is updated, a UserUpdated event shall be dispatched.
     *
     * @return void
     */
    public function test_user_updated_event_dispatched(): void
    {
        $user = User::factory()->createOne();
        $changes = User::factory()->makeOne();

        $user->fill($changes->getAttributes());
        $user->save();

        Event::assertDispatched(UserUpdated::class);
    }

    /**
     * The UserUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function test_user_updated_event_embed_fields(): void
    {
        $user = User::factory()->createOne();
        $changes = User::factory()->makeOne();

        $user->fill($changes->getAttributes());
        $user->save();

        Event::assertDispatched(UserUpdated::class, function (UserUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
