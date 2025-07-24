<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\DumpAction as DumpDatabase;
use App\Actions\Storage\Admin\Dump\DumpWikiAction as DumpWikiDatabase;

class DumpWikiAction extends DumpAction
{
    /**
     * The default name of the action.
     */
    public static function getDefaultName(): ?string
    {
        return 'dump-wiki';
    }

    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.dump.dump.name.wiki'));
    }

    /**
     * Get the underlying action.
     *
     * @param  array<string, mixed>  $data
     * @return DumpDatabase
     */
    protected function storageAction(array $data): DumpDatabase
    {
        return new DumpWikiDatabase($data);
    }
}
