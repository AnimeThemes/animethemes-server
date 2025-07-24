<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface CascadesDeletesEvent
{
    /**
     * Perform cascading deletes.
     */
    public function cascadeDeletes(): void;
}
