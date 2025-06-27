<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\User\Pages;

use App\Filament\Resources\Auth\User;
use App\Filament\Resources\Base\BaseListResources;

/**
 * Class ListUsers.
 */
class ListUsers extends BaseListResources
{
    protected static string $resource = User::class;

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
