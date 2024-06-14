<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\DumpAction as DumpDatabase;
use App\Actions\Storage\Admin\Dump\DumpDocumentAction as DumpDocumentDatabase;

/**
 * Class DumpDocumentTableAction.
 */
class DumpDocumentTableAction extends DumpTableAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.dump.dump.name.document'));
    }

    /**
     * Get the underlying action.
     *
     * @param  array  $fields
     * @return DumpDatabase
     */
    protected function storageAction(array $fields): DumpDatabase
    {
        return new DumpDocumentDatabase($fields);
    }
}
