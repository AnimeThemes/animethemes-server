<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

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
    public function testListening(): void
    {
        $fake = Event::fake();

        $fake->assertListening(CascadesRestoresEvent::class, CascadesRestores::class);
    }
}
