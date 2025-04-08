<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Group\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Wiki\Group;

/**
 * Class ViewGroup.
 */
class ViewGroup extends BaseViewResource
{
    protected static string $resource = Group::class;

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
