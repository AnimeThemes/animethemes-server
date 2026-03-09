<?php

declare(strict_types=1);

use App\Contracts\Events\CreateSynonymEvent;
use App\Listeners\CreateSynonym;
use Illuminate\Support\Facades\Event;

test('listening', function () {
    Event::assertListening(CreateSynonymEvent::class, CreateSynonym::class);
});
