<?php

declare(strict_types=1);

namespace App\Actions\Models;

use App\Actions\ActionResult;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

/**
 * Class BackfillAction.
 *
 * @template TModel of \App\Models\BaseModel
 */
abstract class BackfillAction
{
    /**
     * Create a new action instance.
     *
     * @param  TModel  $model
     */
    public function __construct(protected BaseModel $model)
    {
    }

    /**
     * Handle action.
     *
     * @return ActionResult
     */
    abstract public function handle(): ActionResult;

    /**
     * Get the model the action is handling.
     *
     * @return TModel
     */
    abstract protected function getModel(): BaseModel;

    /**
     * Get the relation to resources.
     *
     * @return Relation
     */
    abstract protected function relation(): Relation;

    /**
     * Get the human-friendly label for the underlying model.
     *
     * @return string
     */
    protected function label(): string
    {
        return Str::headline(class_basename($this->getModel()));
    }
}
