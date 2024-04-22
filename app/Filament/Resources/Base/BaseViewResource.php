<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base;

use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;

/**
 * Class BaseViewResource.
 */
class BaseViewResource extends ViewRecord
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
            EditAction::make()
                ->label(__('filament.actions.base.edit')),

            ForceDeleteAction::make()
                ->label(__('filament.actions.base.forcedelete')),

            RestoreAction::make()
                ->label(__('filament.actions.base.restore')),
        ];
    }
}
