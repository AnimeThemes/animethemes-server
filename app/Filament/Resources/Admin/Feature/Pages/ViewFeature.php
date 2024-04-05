<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Feature\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Admin\Feature;

/**
 * Class ViewFeature.
 */
class ViewFeature extends BaseViewResource
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
