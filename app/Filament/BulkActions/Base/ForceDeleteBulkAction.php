<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use Filament\Actions\ForceDeleteBulkAction as BaseForceDeleteBulkAction;
use Illuminate\Support\Facades\Gate;

/**
 * Class ForceDeleteBulkAction.
 */
class ForceDeleteBulkAction extends BaseForceDeleteBulkAction
{
    use HasActionLogs;

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.bulk_actions.base.forcedelete'));

        $this->visible(fn ($model) => Gate::allows('forceDeleteAny', $model));

        $this->hidden(false);
    }
}
