<?php

declare(strict_types=1);

namespace App\Filament\Resources\Base;

use Filament\Actions\EditAction;
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
        $editPage = (new static::$resource)::getPages()['edit']->getPage();

        return array_merge(
            [
                EditAction::make()
                    ->label(__('filament.actions.base.edit')),
            ],
            (new $editPage)->getHeaderActions(),
        );
    }
}
