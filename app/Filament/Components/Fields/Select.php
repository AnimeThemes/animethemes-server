<?php

declare(strict_types=1);

namespace App\Filament\Components\Fields;

use App\Models\Auth\User;
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
                ->allowHtml()
                ->searchable()
                ->getOptionLabelUsing(fn ($state) => static::getSearchLabelWithBlade((new $model)::find($state)))
                ->getSearchResultsUsing(function (string $search) use ($model, $loadRelation) {
                    return (new $model)::search($search)
                        ->take(25)
                        ->get()
                        ->load($loadRelation ?? [])
                        ->mapWithKeys(fn (BaseModel $model) => [$model->getKey() => static::getSearchLabelWithBlade($model)])
                        ->toArray();
                });
        }

        return $this->searchable();
    }

    /**
     * Use the blade to make the results.
     *
     * @param  BaseModel|User  $model
     * @return string
     */
    public static function getSearchLabelWithBlade(BaseModel|User $model): string
    {
        return view('filament.components.select')
            ->with('name', $model->getName())
            ->with('subtitle', $model->getSubtitle())
            ->with('image', $model instanceof User ? $model->getFilamentAvatarUrl() : null)
            ->render();
    }
}