<?php

declare(strict_types=1);

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Listeners\UpdateRelatedIndices;
use Illuminate\Support\Facades\Event;

test('listening', function () {
    Event::assertListening(UpdateRelatedIndicesEvent::class, UpdateRelatedIndices::class);
});
