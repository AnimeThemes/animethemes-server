<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Base\BaseManageResources;
use App\Filament\Resources\Base\BaseViewResource;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class ViewAction extends Action
{
    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'view';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('');
        $this->modalHeading(fn (): string => __('filament-actions::view.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalSubmitAction(false);
        $this->modalCancelAction(fn (Action $action) => $action->label(__('filament-actions::view.single.modal.actions.close.label')));

        $this->defaultColor('gray');

        $this->tableIcon(Heroicon::Eye);
        $this->groupedIcon(Heroicon::Eye);

        $this->iconSize(IconSize::Medium);

        $this->disabledSchema();

        $this->fillForm(function (HasActions&HasSchemas $livewire, Model $record): array {
            if ($translatableContentDriver = $livewire->makeFilamentTranslatableContentDriver()) {
                $data = $translatableContentDriver->getRecordAttributesToArray($record);
            } else {
                $data = $record->attributesToArray();
            }

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            return $data;
        });

        $this->hidden(fn (BaseManageResources|BaseListResources|BaseViewResource|BaseRelationManager $livewire) => $livewire instanceof BaseViewResource);
    }

    public function mutateRecordDataUsing(?Closure $callback): static
    {
        $this->mutateRecordDataUsing = $callback;

        return $this;
    }
}
