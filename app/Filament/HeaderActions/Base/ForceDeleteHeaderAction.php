<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Base;

use Filament\Actions\ForceDeleteAction as DefaultForceDeleteAction;

/**
 * Class ForceDeleteHeaderAction.
 */
class ForceDeleteHeaderAction extends DefaultForceDeleteAction
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
