<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\ActionLogs\HasPivotActionLogs;
use App\Filament\Components\Fields\Select;
use App\Filament\RelationManagers\BaseRelationManager;
use Filament\Tables\Actions\AttachAction as DefaultAttachAction;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

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

        $this->visible(function (BaseRelationManager $livewire) {
            if (!$livewire->getRelationship() instanceof BelongsToMany) {
                return false;
            }

            $ownerRecord = $livewire->getOwnerRecord();

            $gate = Gate::getPolicyFor($ownerRecord);

            $ability = Str::of('attachAny')
                ->append(Str::singular(class_basename($livewire->getTable()->getModel())))
                ->__toString();

            return is_object($gate) & method_exists($gate, $ability)
                ? Gate::forUser(Auth::user())->any($ability, $ownerRecord)
                : true;
        });

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
