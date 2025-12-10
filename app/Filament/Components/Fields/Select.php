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
                ->getOptionLabelUsing(fn ($state): string => is_null($state) ? '' : BelongsTo::getSearchLabelWithBlade($modelClass::query()->find($state)))
                ->getSearchResultsUsing(
                    fn (string $search) =>
                    /** @phpstan-ignore-next-line */
                    collect(
                        Search::search($modelClass, new Criteria($this->escapeReservedChars($search)))
                            ->passToEloquentBuilder(function (Builder $query) use ($loadRelation, $livewire): void {
                                $query->with($loadRelation ?? []);

                                if (! ($livewire instanceof BaseRelationManager)
                                    ||($livewire->getTable()->allowsDuplicates())) {
                                    return;
                                }

                                // This is necessary to prevent already attached records from being returned on search.
                                $query->whereDoesntHave($livewire->getTable()->getInverseRelationship(), fn (Builder $query) => $query->whereKey($livewire->getOwnerRecord()->getKey()));
                            })
                            ->execute()
                            ->items()
                    )
                        ->mapWithKeys(fn (Model $model): array => [$model->getKey() => BelongsTo::getSearchLabelWithBlade($model)])
                        ->toArray()
                );
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
