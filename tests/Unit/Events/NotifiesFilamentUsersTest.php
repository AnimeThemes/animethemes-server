<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Contracts\Events\FilamentNotificationEvent;
use App\Listeners\NotifiesFilamentUsers;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class NotifiesFilamentUsersTest extends TestCase
{
    /**
     * NotifiesFilamentUsers shall listen to FilamentNotificationEvent.
     */
    public function testListening(): void
    {
        Event::assertListening(FilamentNotificationEvent::class, NotifiesFilamentUsers::class);
    }
}
