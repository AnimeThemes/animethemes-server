<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Feature\Pages;

use App\Filament\Resources\Admin\Feature;
use App\Filament\Resources\Base\BaseEditResource;

/**
 * Class EditFeature.
 */
class EditFeature extends BaseEditResource
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
