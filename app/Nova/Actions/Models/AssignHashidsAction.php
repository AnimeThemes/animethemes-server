<?php

declare(strict_types=1);

namespace App\Nova\Actions\Models;

use App\Actions\Models\AssignHashidsAction as AssignHashids;
use App\Contracts\Models\HasHashids;
use App\Models\BaseModel;
use Exception;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class AssignHashidsAction.
 */
class AssignHashidsAction extends Action
{
    /**
     * Create a new action instance.
     *
     * @param  string|null  $connection
     */
    public function __construct(protected readonly ?string $connection = null)
    {
    }

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.models.assign_hashids.name');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, HasHashids&BaseModel>  $models
     * @return Collection
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(ActionFields $fields, Collection $models): Collection
    {
        $action = new AssignHashids();

        foreach ($models as $model) {
            try {
                $action->assign($model, $this->connection);
            } catch (Exception $e) {
                $this->markAsFailed($model, $e);
            }
        }

        return $models;
    }
}
