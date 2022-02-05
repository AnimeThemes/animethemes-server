<?php

declare(strict_types=1);

namespace App\Contracts\Events;

/**
 * Interface CascadesRestoresEvent.
 */
interface CascadesRestoresEvent
{
    /**
     * Perform cascading restores.
     *
     * @return void
     */
    public function cascadeRestores(): void;
}
