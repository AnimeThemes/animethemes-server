<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;

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

        $this->hidden(false);
    }
}
