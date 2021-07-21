<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Auth;

use App\Events\Auth\User\UserCreated;
use App\Events\Auth\User\UserDeleted;
use App\Events\Auth\User\UserRestored;
use App\Events\Auth\User\UserUpdated;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class UserTest.
 */
class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When a User is created, a UserCreated event shall be dispatched.
     *
     * @return void
     */
    public function testUserCreatedEventDispatched()
    {
        Event::fake();

        User::factory()->createOne();

        Event::assertDispatched(UserCreated::class);
    }

    /**
     * When a User is deleted, a UserDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testUserDeletedEventDispatched()
    {
        Event::fake();

        $user = User::factory()->createOne();

        $user->delete();

        Event::assertDispatched(UserDeleted::class);
    }

    /**
     * When a User is restored, a UserRestored event shall be dispatched.
     *
     * @return void
     */
    public function testUserRestoredEventDispatched()
    {
        Event::fake();

        $user = User::factory()->createOne();

        $user->restore();

        Event::assertDispatched(UserRestored::class);
    }

    /**
     * When a User is updated, a UserUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testUserUpdatedEventDispatched()
    {
        Event::fake();

        $user = User::factory()->createOne();
        $changes = User::factory()->makeOne();

        $user->fill($changes->getAttributes());
        $user->save();

        Event::assertDispatched(UserUpdated::class);
    }
}
