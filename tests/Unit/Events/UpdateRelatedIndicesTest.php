<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Listeners\UpdateRelatedIndices;
use Illuminate\Support\Facades\Event;
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
    public function testListening(): void
    {
        Event::assertListening(UpdateRelatedIndicesEvent::class, UpdateRelatedIndices::class);
    }
}
