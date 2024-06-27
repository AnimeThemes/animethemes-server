<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base;

use App\Filament\HeaderActions\Base\CreateHeaderAction;
use Filament\Resources\Pages\ListRecords;

/**
 * Class BaseListResources.
 */
abstract class BaseListResources extends ListRecords
{
    /**
     * Get the header actions available.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateHeaderAction::make(),
        ];
    }
}
