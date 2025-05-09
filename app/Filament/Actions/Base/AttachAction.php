<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\ActionLogs\HasPivotActionLogs;
use App\Filament\Components\Fields\Select;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Models\Admin\ActionLog;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use Filament\Facades\Filament;
use Filament\Forms\Form;
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

            $select = Select::make('recordId')
                ->label($title)
                ->useScout($livewire, $model)
                ->required();

            if ($this->shouldShowCreateOption($model)) {
                $select = $select
                    ->createOptionForm(fn (Form $form) => Filament::getModelResource($model)::form($form)->getComponents())
                    ->createOptionUsing(function (array $data) use ($model) {
                        $created = $model::query()->create($data);

                        ActionLog::modelCreated($created);

                        return $created->getKey();
                    });
            }

            return $select;
        });

        $this->form(fn (AttachAction $action, BaseRelationManager $livewire): array => [
            $action->getRecordSelect(),
            ...$livewire->getPivotFields(),
        ]);

        $this->after(fn ($livewire, $record, $action) => $this->pivotActionLog('Attach', $livewire, $record, $action));
    }

    /**
     * Determine whether the create option should be shown.
     *
     * @param  string  $model
     * @return bool
     */
    private function shouldShowCreateOption(string $model): bool
    {
        return !($model === Image::class || $model === ExternalResource::class);
    }
}
