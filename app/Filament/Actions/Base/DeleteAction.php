<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use Filament\Actions\DeleteAction as BaseDeleteAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class DeleteAction extends BaseDeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.delete'));

        $this->defaultColor('danger');

        $this->icon(Heroicon::Trash);

        $this->requiresConfirmation();

        $this->using(function (Model $record): bool {
            Gate::authorize('delete', $record);

            $result = $record->delete();

            return (bool) $result;
        });

        $this->authorize(true);
    }
}
