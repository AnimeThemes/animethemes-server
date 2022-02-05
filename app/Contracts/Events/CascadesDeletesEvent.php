<?php

declare(strict_types=1);

namespace App\Contracts\Events;

/**
 * Interface CascadesDeletesEvent.
 */
interface CascadesDeletesEvent
{
    /**
     * Perform cascading deletes.
     *
     * @return void
     */
    public function cascadeDeletes(): void;
}
