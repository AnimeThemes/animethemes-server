<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Admin;

use App\Actions\Storage\Admin\Dump\DumpAction as DumpDatabase;
use App\Actions\Storage\Admin\Dump\DumpDocumentAction as DumpDocumentDatabase;

class DumpDocumentAction extends DumpAction
{
    public static function getDefaultName(): ?string
    {
        return 'dump-document';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.dump.dump.name.document'));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function storageAction(array $data): DumpDatabase
    {
        return new DumpDocumentDatabase($data);
    }
}
