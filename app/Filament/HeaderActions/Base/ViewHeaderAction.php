<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Base;

use App\Filament\Resources\Base\BaseViewResource;
use Filament\Actions\ViewAction as DefaultViewAction;

/**
 * Class ViewHeaderAction.
 */
class ViewHeaderAction extends DefaultViewAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.view'));

        $this->hidden(fn ($livewire) => $livewire instanceof BaseViewResource);
    }
}
