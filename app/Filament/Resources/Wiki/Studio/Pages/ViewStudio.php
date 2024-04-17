<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Studio\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Wiki\Studio;

/**
 * Class ViewStudio.
 */
class ViewStudio extends BaseViewResource
{
    protected static string $resource = Studio::class;

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
