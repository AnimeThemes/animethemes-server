<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Contracts\Events\CascadesRestoresEvent;
use App\Listeners\CascadesRestores;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class CascadesRestoresTest.
 */
class CascadesRestoresTest extends TestCase
{
    /**
     * CascadesRestores shall listen to CascadesRestoresEvent.
     *
     * @return void
     */
    public function test_listening(): void
    {
        Event::assertListening(CascadesRestoresEvent::class, CascadesRestores::class);
    }
}
