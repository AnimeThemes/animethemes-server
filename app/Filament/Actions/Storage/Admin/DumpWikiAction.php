<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\DumpAction as DumpDatabase;
use App\Actions\Storage\Admin\Dump\DumpWikiAction as DumpWikiDatabase;

class DumpWikiAction extends DumpAction
{
    public static function getDefaultName(): ?string
    {
        return 'dump-wiki';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.dump.dump.name.wiki'));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function storageAction(array $data): DumpDatabase
    {
        return new DumpWikiDatabase($data);
    }
}
