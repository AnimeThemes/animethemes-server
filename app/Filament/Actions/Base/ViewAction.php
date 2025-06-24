<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Filament\Resources\Base\BaseViewResource;
use Filament\Actions\ViewAction as BaseViewAction;
use Filament\Support\Enums\IconSize;

/**
 * Class ViewAction.
 */
class ViewAction extends BaseViewAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('');
        $this->iconSize(IconSize::Medium);
        $this->hidden(fn ($livewire) => $livewire instanceof BaseViewResource);
    }
}
