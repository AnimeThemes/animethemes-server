<?php

declare(strict_types=1);

namespace App\Filament\Components\Fields;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Search\Criteria;
use App\Search\Search;
use Filament\Forms\Components\Select as ComponentsSelect;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Select extends ComponentsSelect
{
    /**
     * Use laravel scout to make fields searchable.
     *
     * @param  class-string<Model>  $modelClass
     */
    public function useScout(mixed $livewire, string $modelClass, ?string $loadRelation = null): static
    {
        if (in_array(Searchable::class, class_uses_recursive($modelClass))) {
            return $this
                ->allowHtml()
                ->searchable()
                ->getOptionLabelUsing(fn ($state): string => is_null($state) ? '' : BelongsTo::getSearchLabelWithBlade($modelClass::find($state)))
                ->getSearchResultsUsing(function (string $search) use ($livewire, $modelClass, $loadRelation) {
                    $search = $this->escapeReservedChars($search);

                    /** @phpstan-ignore-next-line */
                    return Search::search($modelClass, new Criteria($search))
                        ->toEloquentBuilder()
                        ->where(function (Builder $query) use ($livewire): void {
                            if (! ($livewire instanceof BaseRelationManager)
                                ||($livewire->getTable()->allowsDuplicates())) {
                                return;
                            }

                            // This is necessary to prevent already attached records from being returned on search.
                            $query->whereDoesntHave($livewire->getTable()->getInverseRelationship(), fn (Builder $query) => $query->whereKey($livewire->getOwnerRecord()->getKey()));
                        })
                        ->get()
                        ->load($loadRelation ?? [])
                        ->mapWithKeys(fn (Model $model): array => [$model->getKey() => BelongsTo::getSearchLabelWithBlade($model)])
                        ->toArray();
                });
        }

        return $this->searchable();
    }

    public function escapeReservedChars(string $search): string
    {
        return preg_replace(
            [
                '_[<>]+_',
                '_[-+=!(){}[\]^"~*?:\\/\\\\]|&(?=&)|\|(?=\|)_',
            ],
            [
                '',
                '\\\\$0',
            ],
            $search
        );
    }
}
