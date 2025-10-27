<?php

declare(strict_types=1);

namespace App\Contracts\Events;

use App\Contracts\Models\HasHashids;
use App\Models\BaseModel;

interface AssignHashidsEvent
{
    /**
     * @return BaseModel&HasHashids
     */
    public function getModel(): BaseModel;

    public function getHashidsConnection(): ?string;
}
