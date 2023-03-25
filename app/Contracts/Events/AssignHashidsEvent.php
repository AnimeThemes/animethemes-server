<?php

declare(strict_types=1);

namespace App\Contracts\Events;

use App\Contracts\Models\HasHashids;
use App\Models\BaseModel;

/**
 * Interface AssignHashidsEvent.
 */
interface AssignHashidsEvent
{
    /**
     * Get the model that has fired this event.
     *
     * @return HasHashids&BaseModel
     */
    public function getModel(): HasHashids&BaseModel;

    /**
     * Get the Hashids connection.
     *
     * @return string|null
     */
    public function getHashidsConnection(): ?string;
}
