<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Storage\Base;

use App\Actions\Storage\Base\DeleteAction as BaseDeleteAction;
use App\Filament\BulkActions\Storage\StorageBulkAction;
use App\Models\BaseModel;
use Filament\Support\Icons\Heroicon;

abstract class DeleteBulkAction extends StorageBulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->color('danger');

        $this->icon(Heroicon::Trash);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  array<string, mixed>  $data
     */
    abstract protected function storageAction(BaseModel $model, array $data): BaseDeleteAction;
}
