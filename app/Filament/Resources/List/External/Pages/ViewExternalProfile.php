<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\External\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\List\ExternalProfile;

/**
 * Class ViewExternalProfile.
 */
class ViewExternalProfile extends BaseViewResource
{
    protected static string $resource = ExternalProfile::class;

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
