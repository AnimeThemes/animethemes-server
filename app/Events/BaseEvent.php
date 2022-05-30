<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\BaseModel;

/**
 * Class BaseEvent.
 *
 * @template TModel of \App\Models\BaseModel
 */
abstract class BaseEvent
{
    /**
     * Create a new event instance.
     *
     * @param  TModel  $model
     */
    public function __construct(protected BaseModel $model)
    {
    }

    /**
     * Get the model that has fired this event.
     *
     * @return TModel
     */
    abstract public function getModel(): BaseModel;
}
