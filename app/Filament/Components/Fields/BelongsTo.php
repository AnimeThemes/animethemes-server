<?php

declare(strict_types=1);

namespace App\Filament\Components\Fields;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\User;
use App\Models\BaseModel;
use Filament\Forms\Components\Select as ComponentsSelect;
use Filament\Forms\Form;
use Laravel\Scout\Searchable;

/**
 * Class BelongsTo.
 */
class BelongsTo extends ComponentsSelect
{
    protected ?BaseResource $resource = null;
    protected bool $showCreateOption = false;

    /**
     * This should reload after every method.
     *
     * @return void
     */
    protected function reload(): void
    {
        $model = $this->resource->getModel();

        if ($this->showCreateOption && $this->resource !== null) {
            $this->createOptionForm(fn (Form $form) => $this->resource::form($form)->getComponents());
            $this->createOptionUsing(fn (array $data) => (new $model)::query()->create($data)->getKey());
        }

        if ($this->resource) {
            $this->label($this->resource->getModelLabel());
        }
    }

    /**
     * Set the filament resource for the relation.
     *
     * @param  class-string<BaseResource>  $resource
     * @return static
     */
    public function resource(string $resource): static
    {
        $this->resource = new $resource;
        $this->tryScout($this->resource->getModel());
        $this->reload();

        return $this;
    }

    /**
     * Determine if the create option is available. The resource is required for this.
     *
     * @param  bool  $condition
     * @return static
     */
    public function showCreateOption(bool $condition = true): static
    {
        $this->showCreateOption = $condition;
        $this->reload();

        return $this;
    }

    /**
     * Make the field searchable and use laravel scout if available.
     *
     * @param  class-string<BaseModel>  $model
     * @return static
     */
    protected function tryScout(string $model): static
    {
        $this->allowHtml();
        $this->searchable();
        $this->getOptionLabelUsing(fn ($state) => static::getSearchLabelWithBlade((new $model)::find($state)));

        if (in_array(Searchable::class, class_uses_recursive($model))) {
            return $this
                ->getSearchResultsUsing(function (string $search) use ($model) {
                    return (new $model)::search($search)
                        ->take(25)
                        ->get()
                        ->mapWithKeys(fn (BaseModel $model) => [$model->getKey() => static::getSearchLabelWithBlade($model)])
                        ->toArray();
                });
        }

        return $this
            ->getSearchResultsUsing(function (string $search) use ($model) {
                return (new $model)::query()
                    ->where($this->resource->getRecordTitleAttribute(), ComparisonOperator::LIKE->value, "%$search%")
                    ->take(25)
                    ->get()
                    ->mapWithKeys(fn (BaseModel|User $model) => [$model->getKey() => static::getSearchLabelWithBlade($model)])
                    ->toArray();
            });
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
