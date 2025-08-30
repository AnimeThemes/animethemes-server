<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface UpdateRelatedIndicesEvent
{
    public function updateRelatedIndices(): void;
}
