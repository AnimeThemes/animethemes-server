<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Contracts\Events\ManagesTrackEvent;
use App\Listeners\ManagesTrack;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ManagesTrackTest extends TestCase
{
    /**
     * ManagesTrack shall listen to ManagesTrackEvent.
     */
    public function testListening(): void
    {
        Event::assertListening(ManagesTrackEvent::class, ManagesTrack::class);
    }
}
