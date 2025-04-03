<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\Performance\Pages;

use App\Filament\Resources\Base\BaseEditResource;
use App\Filament\Resources\Wiki\Song\Performance;

/**
 * Class EditPerformance.
 */
class EditPerformance extends BaseEditResource
{
    protected static string $resource = Performance::class;

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
