<?php

declare(strict_types=1);

namespace App\Contracts\Events;

use App\Contracts\Models\HasHashids;
use App\Models\BaseModel;

interface AssignHashidsEvent
{
    public function getModel(): HasHashids&BaseModel;

    /**
     * Get the Hashids connection.
     */
    public function getHashidsConnection(): ?string;
}
