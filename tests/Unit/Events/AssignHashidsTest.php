<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Contracts\Events\AssignHashidsEvent;
use App\Listeners\AssignHashids;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class AssignHashidsTest.
 */
class AssignHashidsTest extends TestCase
{
    /**
     * AssignHashids shall listen to AssignHashidsEvent.
     *
     * @return void
     */
    public function test_listening(): void
    {
        Event::assertListening(AssignHashidsEvent::class, AssignHashids::class);
    }
}
