<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base;

use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Actions\ForceDeleteAction as ActionsForceDeleteAction;
use Filament\Actions\RestoreAction as ActionsRestoreAction;
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
            ViewAction::make(),
            ActionsDeleteAction::make(),
            ActionsForceDeleteAction::make(),
            ActionsRestoreAction::make(),
        ];
    }
}
