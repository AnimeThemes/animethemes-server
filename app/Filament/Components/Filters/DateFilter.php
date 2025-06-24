<?php

declare(strict_types=1);

namespace App\Filament\Components\Filters;

use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
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
    protected string $fromLabel = 'From';
    protected string $toLabel = 'To';

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
     * Get the schema components for the filter.
     *
     * @return array
     */
    public function getSchemaComponents(): array
    {
        return [
            Fieldset::make($this->getLabel())
                ->label($this->getLabel())
                ->schema([
                    Grid::make([
                        'sm' => 2,
                    ])
                        ->schema([
                            DatePicker::make($this->getName().'_'.'from')
                                ->label($this->fromLabel)
                                ->native(false)
                                ->required(),
                            DatePicker::make($this->getName().'_'.'to')
                                ->label($this->toLabel)
                                ->native(false)
                                ->required(),
                        ]),
                ])
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
                fn (Builder $query, $date): Builder => $query->whereDate($this->getName(), ComparisonOperator::GTE->value, $date),
            )
            ->when(
                Arr::get($data, $this->getName().'_'.'to'),
                fn (Builder $query, $date): Builder => $query->whereDate($this->getName(), ComparisonOperator::LTE->value, $date),
            );
    }
}
