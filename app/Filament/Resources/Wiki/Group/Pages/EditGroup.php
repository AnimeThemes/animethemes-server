<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Group\Pages;

use App\Filament\Resources\Wiki\Group;
use App\Filament\Resources\Base\BaseEditResource;

/**
 * Class EditGroup.
 */
class EditGroup extends BaseEditResource
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
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }
}
