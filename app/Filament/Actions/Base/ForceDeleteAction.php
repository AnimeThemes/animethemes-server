<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use Filament\Actions\ForceDeleteAction as BaseForceDeleteAction;

/**
 * Class ForceDeleteAction.
 */
class ForceDeleteAction extends BaseForceDeleteAction
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
