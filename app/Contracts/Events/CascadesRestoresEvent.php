<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface CascadesRestoresEvent
{
    public function cascadeRestores(): void;
}
