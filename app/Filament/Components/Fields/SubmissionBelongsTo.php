<?php

declare(strict_types=1);

namespace App\Filament\Components\Fields;

use App\Contracts\Models\HasSubtitle;
use App\Contracts\Models\Nameable;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Models\Auth\User;
use App\Search\Criteria;
use App\Search\Search;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class SubmissionBelongsTo extends BelongsTo
{
    protected string $hiddenFieldName;

    public function hiddenField(string $name): static
    {
        $this->hiddenFieldName = $name;

        return $this;
    }

    public function showCreateOption(): static
    {
        $this->createOptionForm(fn (Schema $schema): array => $this->resource::form($schema)->getComponents())
            ->editOptionForm(fn (Schema $schema): array => $this->resource::form($schema)->getComponents())
            ->createOptionUsing(function (Set $set, array $data, SubmissionBelongsTo $component): int {
                $component->state(-1);
                $component->refreshSelectedOptionLabel();
                $set($this->hiddenFieldName, $data);

                return -1;
            })
            ->updateOptionUsing(function (Set $set, array $data, SubmissionBelongsTo $component): int {
                $component->state(-1);
                $component->refreshSelectedOptionLabel();
                $set($this->hiddenFieldName, $data);

                return -1;
            })
            ->fillEditOptionActionFormUsing(fn (Get $get): mixed => $get($this->hiddenFieldName));

        $model = $this->modelResource;
        $this->allowHtml();
        $this->getOptionLabelUsing(function (Get $get, $state) use ($model): string {
            if ($state === '-1') {
                return BelongsTo::getSearchLabelWithBlade(new $model($get($this->hiddenFieldName)));
            }

            return BelongsTo::getSearchLabelWithBlade($model::find($state));
        });

        return $this;
    }

    public function withSubtitle(bool $condition = true): static
    {
        $this->withSubtitle = $condition;

        return $this;
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    protected function tryScout(string $modelClass): static
    {
        $this->searchable();

        if (in_array(Searchable::class, class_uses_recursive($modelClass))) {
            return $this
                ->getSearchResultsUsing(
                    fn (string $search) => collect(
                        Search::search($modelClass, new Criteria($this->escapeReservedChars($search)))
                            ->execute()
                            ->items()
                    )
                        ->mapWithKeys(fn (Model $model): array => [$model->getKey() => static::getSearchLabelWithBlade($model, $this->withSubtitle)])
                        ->toArray()
                );
        }

        return $this
            ->getSearchResultsUsing(
                fn (string $search) => $modelClass::query()
                    ->where($this->resource->getRecordTitleAttribute(), ComparisonOperator::LIKE->value, "%$search%")
                    ->take(25)
                    ->get()
                    ->mapWithKeys(fn ($model): array => [$model->getKey() => static::getSearchLabelWithBlade($model, $this->withSubtitle)])
                    ->toArray()
            );
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
