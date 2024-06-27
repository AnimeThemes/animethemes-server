<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\ActionLog\Pages;

use App\Filament\Resources\Base\BaseViewResource;
use App\Filament\Resources\Admin\ActionLog;

/**
 * Class ViewActionLog.
 */
class ViewActionLog extends BaseViewResource
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
