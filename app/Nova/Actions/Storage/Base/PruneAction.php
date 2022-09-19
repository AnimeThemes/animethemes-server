<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Base;

use App\Actions\Storage\Base\PruneAction as BasePruneAction;
use App\Nova\Actions\Storage\StorageAction;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class PruneAction.
 */
abstract class PruneAction extends StorageAction
{
    /**
     * Get the underlying storage action.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return BasePruneAction
     */
    abstract protected function action(ActionFields $fields, Collection $models): BasePruneAction;

    /**
     * Get the fields available on the action.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(NovaRequest $request): array
    {
        return [
            Number::make(__('nova.actions.storage.prune.fields.hours.name'), 'hours')
                ->help(__('nova.actions.storage.prune.fields.hours.help')),
        ];
    }
}
