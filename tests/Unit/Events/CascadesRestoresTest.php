<?php

declare(strict_types=1);

namespace Events;

use App\Contracts\Events\CascadesRestoresEvent;
use App\Listeners\CascadesRestores;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
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
    public function testListening()
    {
        $fake = Event::fake();

        $listener = Str::of(CascadesRestores::class)
            ->append('@handle')
            ->__toString();

        $fake->assertListening(CascadesRestoresEvent::class, $listener);
    }
}
