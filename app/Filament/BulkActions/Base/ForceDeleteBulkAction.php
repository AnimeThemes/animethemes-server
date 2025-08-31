<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use Filament\Actions\ForceDeleteBulkAction as BaseForceDeleteBulkAction;
use Illuminate\Support\Facades\Gate;

class ForceDeleteBulkAction extends BaseForceDeleteBulkAction
{
    use HasActionLogs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.bulk_actions.base.forcedelete'));

        $this->authorize(true);

        $this->before(fn (string $model) => Gate::authorize('forceDeleteAny', $model));

        $this->hidden(false);
    }
}
