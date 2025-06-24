<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base;

use App\Filament\Actions\Base\CreateAction;
use Filament\Resources\Pages\ManageRecords;

/**
 * Class BaseManageResources.
 */
abstract class BaseManageResources extends ManageRecords
{
    /**
     * Get the header actions available.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
