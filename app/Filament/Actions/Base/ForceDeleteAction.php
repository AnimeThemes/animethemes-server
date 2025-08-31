<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use Filament\Actions\ForceDeleteAction as BaseForceDeleteAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class ForceDeleteAction extends BaseForceDeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.forcedelete'));

        $this->defaultColor('danger');

        $this->icon(Heroicon::Trash);

        $this->requiresConfirmation();

        $this->visible(fn (string $model) => $model::isSoftDeletable());

        $this->using(function (Model $record) {
            Gate::authorize('forceDelete', $record);

            return $record->forceDelete();
        });

        $this->authorize(true);
    }
}
