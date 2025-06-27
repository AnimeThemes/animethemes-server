<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Listeners\CascadesDeletes;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class CascadesDeletesTest.
 */
class CascadesDeletesTest extends TestCase
{
    /**
     * CascadesDeletes shall listen to CascadesDeletesEvent.
     *
     * @return void
     */
    public function test_listening(): void
    {
        Event::assertListening(CascadesDeletesEvent::class, CascadesDeletes::class);
    }
}
