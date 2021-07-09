<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Contracts\Events\CascadesDeletesEvent;
use App\Listeners\CascadesDeletes;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
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
    public function testListening()
    {
        $fake = Event::fake();

        $listener = Str::of(CascadesDeletes::class)
            ->append('@handle')
            ->__toString();

        $fake->assertListening(CascadesDeletesEvent::class, $listener);
    }
}