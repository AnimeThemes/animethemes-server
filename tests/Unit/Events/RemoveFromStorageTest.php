<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Contracts\Events\RemoveFromStorageEvent;
use App\Listeners\Storage\RemoveFromStorage;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class RemoveFromStorageTest.
 */
class RemoveFromStorageTest extends TestCase
{
    /**
     * RemoveFromStorage shall listen to RemoveFromStorageEvent.
     *
     * @return void
     */
    public function test_listening(): void
    {
        Event::assertListening(RemoveFromStorageEvent::class, RemoveFromStorage::class);
    }
}
