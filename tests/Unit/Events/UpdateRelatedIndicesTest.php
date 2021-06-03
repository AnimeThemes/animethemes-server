<?php

declare(strict_types=1);

namespace Events;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Listeners\UpdateRelatedIndices;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class UpdateRelatedIndicesTest.
 */
class UpdateRelatedIndicesTest extends TestCase
{
    /**
     * UpdateRelatedIndices shall listen to UpdateRelatedIndicesEvent.
     *
     * @return void
     */
    public function testListening()
    {
        $fake = Event::fake();

        $listener = Str::of(UpdateRelatedIndices::class)
            ->append('@handle')
            ->__toString();

        $fake->assertListening(UpdateRelatedIndicesEvent::class, $listener);
    }
}
