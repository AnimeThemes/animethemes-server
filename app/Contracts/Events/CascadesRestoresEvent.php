<?php

declare(strict_types=1);

namespace App\Contracts\Events;

/**
 * Interface CascadesRestoresEvent
 * @package App\Contracts\Events
 */
interface CascadesRestoresEvent
{
    /**
     * Perform cascading restores.
     *
     * @return void
     */
    public function cascadeRestores();
}
