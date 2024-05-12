<?php

declare(strict_types=1);

namespace App\Filament\Components\Fields;

use App\Models\BaseModel;
use Filament\Forms\Components\Select as ComponentsSelect;
use Laravel\Scout\Searchable;

/**
 * Class Select.
 */
class Select extends ComponentsSelect
{
    /**
     * Use laravel scout to make fields searchable.
     *
     * @param  class-string<BaseModel>  $model
     * @param  string|null  $loadRelation
     * @return static
     */
    public function useScout(string $model, ?string $loadRelation = null): static
    {
        if (in_array(Searchable::class, class_uses_recursive($model))) {
            return $this
                ->searchable()
                ->getSearchResultsUsing(function (string $search) use ($model, $loadRelation) {
                    $results = (new $model)::search($search)
                        ->get();

                    if ($loadRelation !== null) {
                        $results = $results->load($loadRelation);
                    }

                    return $results
                        ->mapWithKeys(fn (BaseModel $model) => [$model->getKey() => $model->getName()])
                        ->toArray();
                });
        }

        return $this->searchable();
    }
}