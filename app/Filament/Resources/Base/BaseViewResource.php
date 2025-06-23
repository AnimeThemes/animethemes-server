<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base;

use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
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
            EditAction::make(),

            ActionGroup::make([
                DeleteAction::make()
                    ->label(__('filament.actions.base.delete')),

                ForceDeleteAction::make(),
            ])
                ->icon(__('filament-icons.actions.base.group_delete'))
                ->color('danger'),

            RestoreAction::make(),
        ];
    }
}
