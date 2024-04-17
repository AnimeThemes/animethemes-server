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
