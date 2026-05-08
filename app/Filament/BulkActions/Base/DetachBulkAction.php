<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use Filament\Actions\DetachBulkAction as BaseDetachBulkAction;
use Illuminate\Support\Facades\Gate;

class DetachBulkAction extends BaseDetachBulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.bulk_actions.base.detach'));

        $this->authorize(true);

        $this->before(fn (string $model) => Gate::authorize('forceDeleteAny', $model));
    }
}
