<?php

namespace App\Events;

interface CascadesDeletesEvent
{
    /**
     * Perform cascading deletes.
     *
     * @return void
     */
    public function cascadeDeletes();
}
