<?php

declare(strict_types=1);

namespace App\Filament\Resources\User\Report\ReportStep\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\User\Report\ReportStep;

/**
 * Class ListReportSteps.
 */
class ListReportSteps extends BaseListResources
{
    protected static string $resource = ReportStep::class;

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
