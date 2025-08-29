<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface ManagesTrackEvent
{
    public function manageTrack(): void;
}
