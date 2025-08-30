<?php

declare(strict_types=1);

namespace App\Filament\Components\Fields;

use App\Contracts\Models\HasSubtitle;
use App\Contracts\Models\Nameable;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Filament\Resources\BaseResource;
use App\Models\Admin\ActionLog;
use App\Models\Auth\User;
use Filament\Forms\Components\Select as ComponentsSelect;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class BelongsTo extends ComponentsSelect
{
    protected string $relation;
    protected ?BaseResource $resource = null;
    protected bool $showCreateOption = false;
    protected bool $withSubtitle = true;

    protected function reload(): void
    {
        if ($resource = $this->resource) {
            $model = $resource->getModel();
            $this->label($resource->getModelLabel());

            if (filled($this->relation)) {
                $this->relationship($this->relation, $resource->getRecordTitleAttribute());
            }

            $this->tryScout($model);

            if ($this->showCreateOption) {
                $this->createOptionForm(fn (Schema $schema) => $resource::form($schema)->getComponents())
                    ->createOptionUsing(function (array $data) use ($model) {
                        $created = $model::query()->create($data);

                        ActionLog::modelCreated($created);

                        return $created->getKey();
                    });
            }
        }
    }

    /**
     * Set the filament resource for the relation. Relation should be set if BelongsToThrough.
     *
     * @param  class-string<BaseResource>  $resource
     */
    public function resource(string $resource, ?string $relation = ''): static
    {
        $this->resource = new $resource;
        $this->relation = $relation;
        $this->reload();

        return $this;
    }

    public function showCreateOption(bool $condition = true): static
    {
        $this->showCreateOption = $condition;
        $this->reload();

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
