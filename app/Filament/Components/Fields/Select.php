<?php

declare(strict_types=1);

namespace App\Filament\Components\Fields;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Models\BaseModel;
use Filament\Forms\Components\Select as ComponentsSelect;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Scout\Searchable;

/**
 * Class Select.
 */
class Select extends ComponentsSelect
{
    /**
     * Use laravel scout to make fields searchable.
     *
     * @param  mixed  $livewire
     * @param  class-string<BaseModel>  $model
     * @param  string|null  $loadRelation
     * @return static
     */
    public function useScout(mixed $livewire, string $model, ?string $loadRelation = null): static
    {
        if (in_array(Searchable::class, class_uses_recursive($model))) {
            return $this
                ->allowHtml()
                ->searchable()
                ->getOptionLabelUsing(fn ($state) => BelongsTo::getSearchLabelWithBlade($model::find($state)))
                ->getSearchResultsUsing(function (string $search) use ($livewire, $model, $loadRelation) {
                    $search = $this->escapeReservedChars($search);
                    /** @phpstan-ignore-next-line */
                    return $model::search($search)
                        ->query(function (Builder $query) use ($livewire) {

                            if (!($livewire instanceof BaseRelationManager)
                                ||($livewire->getTable()->allowsDuplicates())) {
                                return;
                            }

                            // This is necessary to prevent already attached records from being returned on search.
                            $query->whereDoesntHave($livewire->getTable()->getInverseRelationship(), fn (Builder $query) => $query->whereKey($livewire->getOwnerRecord()->getKey()));
                        })
                        ->take(25)
                        ->get()
                        ->load($loadRelation ?? [])
                        ->mapWithKeys(fn (BaseModel $model) => [$model->getKey() => BelongsTo::getSearchLabelWithBlade($model)])
                        ->toArray();
                });
        }

        return $this->searchable();
    }

    /**
     * Prepare the search query for Elasticsearch.
     *
     * @param  string  $search
     * @return string
     */
    public function escapeReservedChars(string $string) : string
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
            $string
        );
    }
}