<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Base;

use App\Actions\Storage\Base\DeleteAction as BaseDeleteAction;
use App\Nova\Actions\Storage\StorageAction;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class DeleteAction.
 */
abstract class DeleteAction extends StorageAction
{
    /**
     * Get the underlying storage action.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return BaseDeleteAction
     */
    abstract protected function action(ActionFields $fields, Collection $models): BaseDeleteAction;
}
