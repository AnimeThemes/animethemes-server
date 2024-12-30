<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Report\ReportStep\Pages;

use App\Filament\Resources\Admin\Report\ReportStep;
use App\Filament\Resources\Base\BaseListResources;

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
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }
}
