<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base;

use App\Filament\HeaderActions\Base\DeleteHeaderAction;
use App\Filament\HeaderActions\Base\ForceDeleteHeaderAction;
use App\Filament\HeaderActions\Base\RestoreHeaderAction;
use App\Filament\HeaderActions\Base\ViewHeaderAction;
use App\Models\Admin\ActionLog;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\EditRecord;

/**
 * Class BaseEditResource.
 */
abstract class BaseEditResource extends EditRecord
{
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
            ViewHeaderAction::make(),

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

    /**
     * Run after the record is edited.
     *
     * @return void
     */
    protected function afterSave(): void
    {
        ActionLog::modelUpdated($this->getRecord());
    }
}
