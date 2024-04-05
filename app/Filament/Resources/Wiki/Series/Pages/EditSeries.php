<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Series\Pages;

use App\Filament\Resources\Wiki\Series;
use App\Filament\Resources\Base\BaseEditResource;

/**
 * Class EditSeries.
 */
class EditSeries extends BaseEditResource
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
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }
}
