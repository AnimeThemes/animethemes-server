<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Contracts\Events\RemoveFromStorageEvent;
use App\Listeners\Storage\RemoveFromStorage;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RemoveFromStorageTest extends TestCase
{
    /**
     * RemoveFromStorage shall listen to RemoveFromStorageEvent.
     */
    public function testListening(): void
    {
        Event::assertListening(RemoveFromStorageEvent::class, RemoveFromStorage::class);
    }
}
