<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use Filament\Actions\RestoreBulkAction as BaseRestoreBulkAction;
use Illuminate\Support\Facades\Gate;

class RestoreBulkAction extends BaseRestoreBulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.bulk_actions.base.restore'));

        $this->authorize(true);

        $this->before(fn (string $model) => Gate::authorize('restoreAny', $model));
    }
}
