<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Contracts\Models\SoftDeletable;
use App\Models\Admin\ActionLog;
use Filament\Actions\RestoreAction as BaseRestoreAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class RestoreAction extends BaseRestoreAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.restore'));

        $this->defaultColor('gray');

        $this->icon(Heroicon::ArrowUturnLeft);

        $this->requiresConfirmation();

        $this->visible(static function (Model $record): bool {
            if (! method_exists($record, 'trashed')) {
                return false;
            }

            return $record->trashed();
        });

        $this->after(fn (Model $record) => ActionLog::modelRestored($record));

        $this->using(function (Model&SoftDeletable $record) {
            Gate::authorize('restore', $record);

            $result = $record->restore();

            return (bool) $result;
        });

        $this->authorize(true);
    }
}
