<?php

declare(strict_types=1);

namespace App\Actions\Models;

use App\Actions\ActionResult;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

/**
 * @template TModel of \App\Models\BaseModel
 */
abstract class BackfillAction
{
    /**
     * @param  TModel  $model
     */
    public function __construct(protected BaseModel $model) {}

    /**
/
    abstract public function handle(): ActionResult;

    /**
     * Get the model the action is handling.
     *
     * @return TModel
     */
    abstract protected function getModel(): BaseModel;

    /**
     * Get the relation to resources.
     */
    abstract protected function relation(): Relation;

    /**
     * Get the human-friendly label for the underlying model.
     */
    protected function label(): string
    {
        return Str::headline(class_basename($this->getModel()));
    }
}
