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
    protected string $attribute = '';
    protected string $fromLabel = '';
    protected string $toLabel = '';

    /**
     * Get the attribute used for filter.
     *
     * @param  string  $attribute
     * @return static
     */
    public function attribute(string $attribute): static
    {
        $this->attribute = $attribute;

        return $this;
    }

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
            TextInput::make($this->attribute.'_'.'from')
                ->label($this->fromLabel)
                ->integer(),

            TextInput::make($this->attribute.'_'.'to')
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
                Arr::get($data, $this->attribute.'_'.'from'),
                fn (Builder $query, $value): Builder => $query->where($this->attribute, ComparisonOperator::GTE->value, $value),
            )
            ->when(
                Arr::get($data, $this->attribute.'_'.'to'),
                fn (Builder $query, $value): Builder => $query->where($this->attribute, ComparisonOperator::LTE->value, $value),
            );
    }
}