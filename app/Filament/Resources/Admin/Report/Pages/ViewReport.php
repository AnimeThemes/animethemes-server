<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Report\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Admin\Report;

/**
 * Class ViewReport.
 */
class ViewReport extends BaseViewResource
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
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }
}
