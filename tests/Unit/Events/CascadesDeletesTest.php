<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Listeners\CascadesDeletes;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CascadesDeletesTest extends TestCase
{
    /**
     * CascadesDeletes shall listen to CascadesDeletesEvent.
     */
    public function testListening(): void
    {
        Event::assertListening(CascadesDeletesEvent::class, CascadesDeletes::class);
    }
}
