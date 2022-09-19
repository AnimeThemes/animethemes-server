<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\DumpAction as DumpDatabase;
use App\Actions\Storage\Admin\Dump\DumpDocumentAction as DumpDocumentDatabase;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class DumpDocumentAction.
 */
class DumpDocumentAction extends DumpAction
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
        return __('nova.actions.dump.dump.name.document');
    }

    /**
     * Get the underlying action.
     *
     * @param  ActionFields  $fields
     * @return DumpDatabase
     */
    protected function action(ActionFields $fields): DumpDatabase
    {
        return new DumpDocumentDatabase($fields->toArray());
    }
}
