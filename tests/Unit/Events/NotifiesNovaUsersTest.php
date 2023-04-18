<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Contracts\Events\NovaNotificationEvent;
use App\Listeners\NotifiesNovaUsers;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class NotifiesNovaUsersTest.
 */
class NotifiesNovaUsersTest extends TestCase
{
    /**
     * NotifiesNovaUsers shall listen to NovaNotificationEvent.
     *
     * @return void
     */
    public function testListening(): void
    {
        Event::assertListening(NovaNotificationEvent::class, NotifiesNovaUsers::class);
    }
}
