<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\DumpAction as DumpDatabase;
use App\Actions\Storage\Admin\Dump\DumpWikiAction as DumpWikiDatabase;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class DumpWikiAction.
 */
class DumpWikiAction extends DumpAction
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
        return __('nova.actions.dump.dump.name.wiki');
    }

    /**
     * Get the underlying action.
     *
     * @param  ActionFields  $fields
     * @return DumpDatabase
     */
    protected function action(ActionFields $fields): DumpDatabase
    {
        return new DumpWikiDatabase($fields->toArray());
    }
}
