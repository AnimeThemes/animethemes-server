<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\ActionLog\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Admin\ActionLog;

/**
 * Class ListActionLogs.
 */
class ListActionLogs extends BaseListResources
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
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }
}
