<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\ActionLog\Pages;

use App\Filament\Resources\Admin\ActionLog;
use App\Filament\Resources\Base\BaseManageResources;

/**
 * Class ManageActionLogs.
 */
class ManageActionLogs extends BaseManageResources
{
    protected static string $resource = ActionLog::class;

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
