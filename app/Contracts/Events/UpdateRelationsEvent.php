<?php

declare(strict_types=1);

namespace App\Contracts\Events;

interface UpdateRelationsEvent
{
    public function updateRelations(): void;
}
