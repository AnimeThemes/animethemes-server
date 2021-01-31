<?php

namespace App\Contracts\Events;

interface CascadesDeletesEvent
{
    /**
     * Perform cascading deletes.
     *
     * @return void
     */
    public function cascadeDeletes();
}
