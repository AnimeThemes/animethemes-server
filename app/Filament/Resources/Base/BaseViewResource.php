<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base;

use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;
use Filament\Resources\Pages\ViewRecord;

class BaseViewResource extends ViewRecord
{
    use HasRecentHistoryRecorder;

    protected $listeners = [
        'updateAllRelationManager' => '$refresh',
    ];

    /**
     * Get the header actions available.
     *
     * @return \Filament\Actions\Action[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        return static::$resource::getActions();
    }
}
