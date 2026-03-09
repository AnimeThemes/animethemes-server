<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface CreateSynonymEvent
{
    public function createSynonym(): void;
}
