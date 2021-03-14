<?php

namespace App\Contracts\Events;

interface CascadesRestoresEvent
{
    /**
     * Perform cascading restores.
     *
     * @return void
     */
    public function cascadeRestores();
}
