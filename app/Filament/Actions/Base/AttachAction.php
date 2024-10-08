<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\Actions\HasPivotActionLogs;
use App\Filament\Components\Fields\Select;
use App\Filament\RelationManagers\BaseRelationManager;
use Filament\Tables\Actions\AttachAction as DefaultAttachAction;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class AttachAction.
 */
class AttachAction extends DefaultAttachAction
{
    use HasPivotActionLogs;

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->authorize('create');

        $this->hidden(fn (BaseRelationManager $livewire) => !($livewire->getRelationship() instanceof BelongsToMany));

        $this->recordSelect(function (BaseRelationManager $livewire) {
            /** @var string */
            $model = $livewire->getTable()->getModel();
            $title = $livewire->getTable()->getRecordTitle(new $model);
            return Select::make('recordId')
                ->label($title)
                ->useScout($model)
                ->required();
        });

        $this->form(fn (AttachAction $action, BaseRelationManager $livewire): array => [
            $action->getRecordSelect(),
            ...$livewire->getPivotFields(),
        ]);

        $this->after(fn ($livewire, $record) => $this->pivotActionLog('Attach', $livewire, $record));
    }
}
