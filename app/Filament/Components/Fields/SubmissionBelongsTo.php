<?php

declare(strict_types=1);

namespace App\Filament\Components\Fields;

use App\Contracts\Models\HasSubtitle;
use App\Contracts\Models\Nameable;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Models\Auth\User;
use App\Models\User\Submission\SubmissionVirtual;
use App\Search\Criteria;
use App\Search\Search;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;

class SubmissionBelongsTo extends BelongsTo
{
    public function showCreateOption(): static
    {
        $model = $this->modelResource;

        $this->createOptionForm(fn (Schema $schema): array => $this->resource::form($schema)->getComponents())
            ->editOptionForm(fn (Schema $schema): array => $this->resource::form($schema)->getComponents())
            ->editOptionAction(fn (Action $action, $state): Action => $action->visible($state < 0))
            ->createOptionUsing(function (array $data, SubmissionBelongsTo $component): int {
                $virtual = SubmissionVirtual::query()
                    ->create([
                        SubmissionVirtual::ATTRIBUTE_MODEL_TYPE => $this->modelResource,
                        SubmissionVirtual::ATTRIBUTE_FIELDS => $data,
                        SubmissionVirtual::ATTRIBUTE_USER => Auth::id(),
                    ]);

                $component->state(-$virtual->getKey());
                $component->refreshSelectedOptionLabel();

                return -$virtual->getKey();
            })
            ->updateOptionUsing(function ($state, array $data, SubmissionBelongsTo $component): int {
                $state = intval($state);

                SubmissionVirtual::query()
                    ->find($state * -1)
                    ->update([
                        SubmissionVirtual::ATTRIBUTE_FIELDS => $data,
                    ]);

                $component->state($state);
                $component->refreshSelectedOptionLabel();

                return $state;
            })
            ->fillEditOptionActionFormUsing(fn ($state): mixed => SubmissionVirtual::query()->find(intval($state) * -1)->fields);

        $this->allowHtml();
        $this->getOptionLabelUsing(function ($state) use ($model): string {
            $state = intval($state);
            if ($state <= 0) {
                $virtual = SubmissionVirtual::query()->find($state * -1);

                return static::getSearchLabelWithBlade(new $model($virtual->fields));
            }

            return static::getSearchLabelWithBlade($model::find($state));
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
                        ->union($this->getVirtuals())
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
                    ->union($this->getVirtuals())
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
            ->render();
    }

    protected function getVirtuals(): Collection
    {
        return SubmissionVirtual::query()
            ->whereBelongsTo(Auth::user())
            ->where(SubmissionVirtual::ATTRIBUTE_EXISTS, false)
            ->where(SubmissionVirtual::ATTRIBUTE_MODEL_TYPE, Relation::getMorphAlias($this->modelResource))
            ->get()
            ->mapWithKeys(fn (SubmissionVirtual $virtual): array => [-$virtual->getKey() => static::getSearchLabelWithBlade(new $virtual->model($virtual->fields), $this->withSubtitle)]);
    }
}
