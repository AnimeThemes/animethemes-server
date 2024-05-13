<?php

declare(strict_types=1);

namespace App\Filament\Components\Filters;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * Class DateFilter.
 */
class DateFilter extends Filter
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
            DatePicker::make($this->attribute.'_'.'from')
                ->label($this->fromLabel),

            DatePicker::make($this->attribute.'_'.'to')
                ->label($this->toLabel),
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
                fn (Builder $query, $date): Builder => $query->whereDate($this->attribute, ComparisonOperator::GTE->value, $date),
            )
            ->when(
                Arr::get($data, $this->attribute.'_'.'to'),
                fn (Builder $query, $date): Builder => $query->whereDate($this->attribute, ComparisonOperator::LTE->value, $date),
            );
    }
}