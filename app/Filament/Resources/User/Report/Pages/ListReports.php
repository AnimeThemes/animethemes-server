<?php

declare(strict_types=1);

namespace App\Filament\Resources\User\Report\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\User\Report;

/**
 * Class ListReports.
 */
class ListReports extends BaseListResources
{
    protected static string $resource = Report::class;

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
            ...parent::getHeaderActions(),
        ];
    }
}
