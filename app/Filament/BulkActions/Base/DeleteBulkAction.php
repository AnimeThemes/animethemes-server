<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use Filament\Actions\DeleteBulkAction as BaseDeleteBulkAction;
use Illuminate\Support\Facades\Gate;

class DeleteBulkAction extends BaseDeleteBulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.bulk_actions.base.delete'));

        $this->authorize(true);

        $this->before(fn (string $model) => Gate::authorize('forceDeleteAny', $model));
    }
}
