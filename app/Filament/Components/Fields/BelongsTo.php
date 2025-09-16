<?php

declare(strict_types=1);

namespace App\Filament\Components\Fields;

use App\Contracts\Models\HasSubtitle;
use App\Contracts\Models\Nameable;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\User;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class BelongsTo extends Select
{
    /** @var class-string<Model>|null */
    protected ?string $modelResource = null;
    protected string $relation;
    protected ?BaseResource $resource = null;
    protected bool $withSubtitle = true;

    /**
     * Set the filament resource for the relation. Relation should be set if BelongsToThrough.
     *
     * @param  class-string<BaseResource>  $resource
     */
    public function resource(string $resource, ?string $relation = ''): static
    {
        $this->resource = new $resource;
        $this->modelResource = $this->resource->getModel();
        $this->relation = $relation;

        $this->label($this->resource->getModelLabel());

        if (filled($this->relation)) {
            $this->relationship($this->relation, $this->resource->getRecordTitleAttribute());
        }

        $this->tryScout($this->modelResource);

        return $this;
    }

    public function showCreateOption(): static
    {
        $this->createOptionForm(fn (Schema $schema) => $this->resource::form($schema)->getComponents())
            ->createOptionUsing(fn (array $data) => $this->resource->getModel()::query()->create($data)->getKey());

        return $this;
    }

    public function withSubtitle(bool $condition = true): static
    {
        $this->withSubtitle = $condition;

        return $this;
    }

    /**
     * @param  class-string<Model>  $model
     */
    protected function tryScout(string $model): static
    {
        $this->allowHtml();
        $this->searchable();
        $this->getOptionLabelUsing(fn ($state) => is_null($state) ? '' : static::getSearchLabelWithBlade($model::find($state), $this->withSubtitle));

        if (in_array(Searchable::class, class_uses_recursive($model))) {
            return $this
                ->getSearchResultsUsing(function (string $search) use ($model) {
                    $search = $this->escapeReservedChars($search);

                    /** @phpstan-ignore-next-line */
                    return $model::search($search)
                        ->take(25)
                        ->get()
                        ->mapWithKeys(fn (Model $model) => [$model->getKey() => static::getSearchLabelWithBlade($model, $this->withSubtitle)])
                        ->toArray();
                });
        }

        return $this
            ->getSearchResultsUsing(function (string $search) use ($model) {
                return $model::query()
                    ->where($this->resource->getRecordTitleAttribute(), ComparisonOperator::LIKE->value, "%$search%")
                    ->take(25)
                    ->get()
                    ->mapWithKeys(fn ($model) => [$model->getKey() => static::getSearchLabelWithBlade($model, $this->withSubtitle)])
                    ->toArray();
            });
    }

    /**
     * @param  Model|User|Nameable|HasSubtitle  $model
     */
    public static function getSearchLabelWithBlade($model, bool $withSubtitle = true): string
    {
        return view('filament.components.select')
            ->with('name', $model->getName())
            ->with('subtitle', $withSubtitle ? $model->getSubtitle() : null)
            ->with('image', $model instanceof User ? $model->getFilamentAvatarUrl() : null)
            ->render();
    }
}
