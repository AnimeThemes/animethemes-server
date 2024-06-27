<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use Filament\Tables\Actions\ForceDeleteAction as DefaultForceDeleteAction;

/**
 * Class ForceDeleteAction.
 */
class ForceDeleteAction extends DefaultForceDeleteAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.forcedelete'));

        $this->visible(true);
    }
}
