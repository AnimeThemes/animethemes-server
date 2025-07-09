<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use Illuminate\Support\Facades\Auth;

/**
 * Class ForceDeleteBulkAction.
 */
class ForceDeleteBulkAction extends \Filament\Actions\ForceDeleteBulkAction
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

        $this->visible(fn ($model) => Auth::user()->can('forcedeleteany', $model));

        $this->hidden(false);
    }
}
