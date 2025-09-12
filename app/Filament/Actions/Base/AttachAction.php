<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\ActionLogs\HasPivotActionLogs;
use App\Filament\Components\Fields\Select;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Models\Admin\ActionLog;
use App\Models\Wiki\Image;
use Filament\Actions\AttachAction as BaseAttachAction;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class AttachAction extends BaseAttachAction
{
    use HasPivotActionLogs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(function (BaseRelationManager $livewire) {
            if (! $livewire->getRelationship() instanceof BelongsToMany) {
                return false;
            }

            $ownerRecord = $livewire->getOwnerRecord();

            $gate = Gate::getPolicyFor($ownerRecord);

            $ability = Str::of('attachAny')
                ->append(Str::studly(class_basename($livewire->getTable()->getModel())))
                ->__toString();

            return is_object($gate) & method_exists($gate, $ability)
                ? Gate::forUser(Auth::user())->check($ability, $ownerRecord::class)
                : true;
        });

        $this->recordSelect(function (BaseRelationManager $livewire) {
            $model = $livewire->getTable()->getModel();
            $title = $livewire->getTable()->getRecordTitle(new $model);

            $select = Select::make('recordId')
                ->label($title)
                ->useScout($livewire, $model)
                ->required();

            if ($this->shouldShowCreateOption($model)) {
                $select = $select
                    ->createOptionForm(fn (Schema $schema) => Filament::getModelResource($model)::form($schema)->getComponents())
                    ->createOptionUsing(function (array $data) use ($model) {
                        $created = $model::query()->create($data);

                        ActionLog::modelCreated($created);

                        return $created->getKey();
                    });
            }

            return $select;
        });

        $this->schema(fn (AttachAction $action, BaseRelationManager $livewire): array => [
            $action->getRecordSelect(),
            ...$livewire->getPivotComponents(),
        ]);

        $this->after(fn (BaseRelationManager $livewire, Model $record, AttachAction $action) => $this->pivotActionLog('Attach', $livewire, $record, $action));
    }

    /**
     * Determine whether the create option should be shown.
     *
     * @param  class-string<Model>  $model
     */
    private function shouldShowCreateOption(string $model): bool
    {
        return ! ($model === Image::class);
    }
}
