<?php

declare(strict_types=1);

use App\Contracts\Events\CascadesRestoresEvent;
use App\Listeners\CascadesRestores;
use Illuminate\Support\Facades\Event;

test('listening', function () {
    Event::assertListening(CascadesRestoresEvent::class, CascadesRestores::class);
});
