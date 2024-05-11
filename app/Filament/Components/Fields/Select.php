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
     * @return static
     */
    public function useScout(string $model): static
    {
        if (in_array(Searchable::class, class_uses_recursive($model))) {
            return $this
                ->searchable()
                ->getSearchResultsUsing(function (string $search) use ($model) {
                    return (new $model)::search($search)
                        ->get()
                        ->mapWithKeys(fn (BaseModel $model) => [$model->getKey() => $model->getName()])
                        ->toArray();
                });
        }

        return $this->searchable();
    }
}