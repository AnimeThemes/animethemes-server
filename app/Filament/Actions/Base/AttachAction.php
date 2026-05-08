<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Filament\Components\Fields\Select;
use App\Filament\RelationManagers\BaseRelationManager;
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

            return (is_object($gate) & method_exists($gate, $ability)) !== 0
                ? Gate::forUser(Auth::user())->check($ability, $ownerRecord::class)
                : true;
        });

        $this->recordSelect(function (BaseRelationManager $livewire): Select {
            $model = $livewire->getTable()->getModel();
            $title = $livewire->getTable()->getRecordTitle(new $model);

            $select = Select::make('recordId')
                ->label($title)
                ->useScout($livewire, $model)
                ->required();

            if ($this->shouldShowCreateOption($model)) {
                return $select
                    ->createOptionForm(fn (Schema $schema) => Filament::getModelResource($model)::form($schema)->getComponents())
                    ->createOptionUsing(fn (array $data) => $model::query()->create($data)->getKey());
            }

            return $select;
        });

        $this->schema(fn (AttachAction $action, BaseRelationManager $livewire): array => [
            $action->getRecordSelect(),
            ...$livewire->getPivotComponents(),
        ]);
    }

    /**
     * Determine whether the create option should be shown.
     *
     * @param  class-string<Model>  $model
     */
    private function shouldShowCreateOption(string $model): bool
    {
        return $model !== Image::class;
    }
}
