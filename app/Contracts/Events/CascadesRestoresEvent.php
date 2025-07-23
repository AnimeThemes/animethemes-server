<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface CascadesRestoresEvent
{
    /**
     * Perform cascading restores.
     */
    public function cascadeRestores(): void;
}
