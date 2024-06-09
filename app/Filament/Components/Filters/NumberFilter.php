<?php

declare(strict_types=1);

namespace App\Filament\Components\Filters;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * Class NumberFilter.
 */
class NumberFilter extends Filter
{
    protected string $fromLabel = '';
    protected string $toLabel = '';

    /**
     * Get the label for the filters.
     *
     * @param  string  $fromLabel
     * @param  string  $toLabel
     *
     * @return static
     */
    public function labels(string $fromLabel, string $toLabel): static
    {
        $this->fromLabel = $fromLabel;
        $this->toLabel = $toLabel;

        return $this;
    }

    /**
     * Get the form for the filter.
     *
     * @return array
     */
    public function getFormSchema(): array
    {
        return [
            TextInput::make($this->getName().'_'.'from')
                ->label($this->fromLabel)
                ->integer(),

            TextInput::make($this->getName().'_'.'to')
                ->label($this->toLabel)
                ->integer(),
        ];
    }

    /**
     * Apply the query used to the filter.
     *
     * @param  Builder  $query
     * @param  array  $data
     * @return Builder
     */
    public function applyToBaseQuery(Builder $query, array $data = []): Builder
    {
        return $query
            ->when(
                Arr::get($data, $this->getName().'_'.'from'),
                fn (Builder $query, $value): Builder => $query->where($this->getName(), ComparisonOperator::GTE->value, $value),
            )
            ->when(
                Arr::get($data, $this->getName().'_'.'to'),
                fn (Builder $query, $value): Builder => $query->where($this->getName(), ComparisonOperator::LTE->value, $value),
            );
    }
}