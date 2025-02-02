<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\Membership\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Song\Membership;

/**
 * Class ListMemberships.
 */
class ListMemberships extends BaseListResources
{
    protected static string $resource = Membership::class;

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
