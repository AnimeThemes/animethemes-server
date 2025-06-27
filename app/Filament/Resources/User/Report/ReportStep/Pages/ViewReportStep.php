<?php

declare(strict_types=1);

namespace App\Filament\Resources\User\Report\ReportStep\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\User\Report\ReportStep;

/**
 * Class ViewReportStep.
 */
class ViewReportStep extends BaseViewResource
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
