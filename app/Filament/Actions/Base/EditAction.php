<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use App\Concerns\Filament\ActionLogs\HasPivotActionLogs;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Base\BaseManageResources;
use App\Filament\Resources\Base\BaseViewResource;
use App\Models\Admin\ActionLog;
use Filament\Actions\EditAction as BaseEditAction;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Gate;

class EditAction extends BaseEditAction
{
    use HasActionLogs;
    use HasPivotActionLogs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (): string => '');

        $this->icon(Heroicon::PencilSquare);
        $this->iconSize(IconSize::Medium);

        $this->schema(fn (Schema $schema, BaseListResources|BaseViewResource|BaseManageResources|BaseRelationManager $livewire): array => [
            ...$livewire->form($schema)->getComponents(),
            ...($livewire instanceof BaseRelationManager ? $livewire->getPivotComponents() : []),
        ]);

        $this->after(function (BaseListResources|BaseViewResource|BaseManageResources|BaseRelationManager $livewire, Model $record, EditAction $action): void {
            if ($livewire instanceof BaseListResources || $livewire instanceof BaseViewResource) {
                ActionLog::modelUpdated($record);
            }

            if ($livewire instanceof BaseRelationManager && $livewire->getRelationship() instanceof BelongsToMany) {
                $this->pivotActionLog('Update Attached', $livewire, $record, $action);
            }
        });

        $this->beforeFormFilled(function (Model $record): void {
            Gate::authorize('update', $record);
        });

        $this->authorize(true);
    }
}
