<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Feature\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Admin\Feature;

/**
 * Class ListFeatures.
 */
class ListFeatures extends BaseListResources
{
    protected static string $resource = Feature::class;

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
