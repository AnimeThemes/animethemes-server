<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base;

use App\Filament\HeaderActions\Base\DeleteHeaderAction;
use App\Filament\HeaderActions\Base\EditHeaderAction;
use App\Filament\HeaderActions\Base\ForceDeleteHeaderAction;
use App\Filament\HeaderActions\Base\RestoreHeaderAction;
use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ViewRecord;

/**
 * Class BaseViewResource.
 */
class BaseViewResource extends ViewRecord
{
    use HasRecentHistoryRecorder;

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
            EditHeaderAction::make(),

            ActionGroup::make([
                DeleteHeaderAction::make()
                    ->label(__('filament.actions.base.delete')),

                ForceDeleteHeaderAction::make(),
            ])
                ->icon(__('filament-icons.actions.base.group_delete'))
                ->color('danger'),

            RestoreHeaderAction::make(),
        ];
    }
}
