<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Contracts\Events\NovaNotificationEvent;
use App\Listeners\CascadesDeletes;
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
        $fake = Event::fake();

        $fake->assertListening(NovaNotificationEvent::class, NotifiesNovaUsers::class);
    }
}
