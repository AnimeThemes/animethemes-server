<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base;

//use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;
use Filament\Resources\Pages\ViewRecord;

/**
 * Class BaseViewResource.
 */
class BaseViewResource extends ViewRecord
{
    //use HasRecentHistoryRecorder;

    /**
     * Get the header actions available.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        return static::$resource::getActions();
    }
}
