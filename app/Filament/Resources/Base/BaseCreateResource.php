<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base;

use App\Models\Admin\ActionLog;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class BaseCreateResource.
 */
abstract class BaseCreateResource extends CreateRecord
{
    /**
     * Run after the record is created.
     *
     * @return void
     */
    protected function afterCreate(): void
    {
        ActionLog::modelCreated($this->getRecord());
    }
}
