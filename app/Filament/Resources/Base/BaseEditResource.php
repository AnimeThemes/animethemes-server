<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
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
            ViewAction::make()
                ->label(__('filament.actions.base.view'))
                ->hidden(fn ($livewire) => $livewire instanceof BaseViewResource),

            ActionGroup::make([
                DeleteAction::make()
                    ->label(__('filament.actions.base.delete')),

                ForceDeleteAction::make()
                    ->label(__('filament.actions.base.forcedelete'))
                    ->visible(true),
            ])
                ->icon('heroicon-o-trash')
                ->color('danger'),

            RestoreAction::make()
                ->label(__('filament.actions.base.restore')),
        ];
    }
}
