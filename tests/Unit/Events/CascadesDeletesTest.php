<?php

declare(strict_types=1);

use App\Contracts\Events\CascadesDeletesEvent;
use App\Listeners\CascadesDeletes;
use Illuminate\Support\Facades\Event;

test('listening', function (): void {
    Event::assertListening(CascadesDeletesEvent::class, CascadesDeletes::class);
});
