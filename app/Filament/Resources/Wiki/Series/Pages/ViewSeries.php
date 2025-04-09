<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Series\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Wiki\Series;

/**
 * Class ViewSeries.
 */
class ViewSeries extends BaseViewResource
{
    protected static string $resource = Series::class;

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
