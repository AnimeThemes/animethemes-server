<?php declare(strict_types=1);

namespace App\Contracts\Events;

/**
 * Interface CascadesDeletesEvent
 * @package App\Contracts\Events
 */
interface CascadesDeletesEvent
{
    /**
     * Perform cascading deletes.
     *
     * @return void
     */
    public function cascadeDeletes();
}
