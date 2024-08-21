<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Feature\Pages;

use App\Filament\Resources\Base\BaseManageResources;
use App\Filament\Resources\Admin\Feature;

/**
 * Class ManageFeatures.
 */
class ManageFeatures extends BaseManageResources
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
        return [];
    }
}
