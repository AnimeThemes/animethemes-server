<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\PruneDumpAction as PruneDump;
use App\Nova\Actions\Storage\Base\PruneAction;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class PruneDumpAction.
 */
class PruneDumpAction extends PruneAction
{
    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.dump.prune.name');
    }

    /**
     * Get the underlying storage action.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return PruneDump
     */
    protected function action(ActionFields $fields, Collection $models): PruneDump
    {
        $hours = $fields->get('hours');

        return new PruneDump(intval($hours));
    }
}
