<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Base\BaseManageResources;
use App\Filament\Resources\Base\BaseViewResource;
use Filament\Actions\ViewAction as BaseViewAction;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;

class ViewAction extends BaseViewAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('');

        $this->defaultColor('gray');

        $this->icon(Heroicon::Eye);

        $this->iconSize(IconSize::Medium);

        $this->hidden(fn (BaseManageResources|BaseListResources|BaseViewResource|BaseRelationManager $livewire): bool => $livewire instanceof BaseViewResource);

        $this->authorize(true);
    }
}
