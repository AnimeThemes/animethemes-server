<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Base;

use Filament\Actions\EditAction as DefaultEditAction;
use Filament\Support\Enums\IconSize;

/**
 * Class EditHeaderAction.
 */
class EditHeaderAction extends DefaultEditAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.edit'));
        $this->icon('heroicon-o-pencil-square');
    }
}
