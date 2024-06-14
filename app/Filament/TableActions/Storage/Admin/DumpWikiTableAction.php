<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\DumpAction as DumpDatabase;
use App\Actions\Storage\Admin\Dump\DumpWikiAction as DumpWikiDatabase;

/**
 * Class DumpWikiTableAction.
 */
class DumpWikiTableAction extends DumpTableAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.dump.dump.name.wiki'));
    }

    /**
     * Get the underlying action.
     *
     * @param  array  $fields
     * @return DumpDatabase
     */
    protected function storageAction(array $fields): DumpDatabase
    {
        return new DumpWikiDatabase($fields);
    }
}
