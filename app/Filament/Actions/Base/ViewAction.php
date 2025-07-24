<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Base\BaseManageResources;
use App\Filament\Resources\Base\BaseViewResource;
use Filament\Actions\ViewAction as BaseViewAction;
use Filament\Support\Enums\IconSize;

class ViewAction extends BaseViewAction
{
    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('');
        $this->iconSize(IconSize::Medium);
        $this->hidden(fn (BaseManageResources|BaseListResources|BaseViewResource|BaseRelationManager $livewire) => $livewire instanceof BaseViewResource);
    }
}
