<?php

declare(strict_types=1);

namespace App\Filament\Components\Filters;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * Class NumberFilter.
 */
class NumberFilter extends Filter
{
    /**
     * Get the form for the filter.
     *
     * @return array
     */
    public function getFormSchema(): array
    {
        return [
            Fieldset::make($this->getLabel())
                ->label($this->getLabel())
                ->schema([
                    Grid::make([
                        'sm' => 2,
                    ])
                        ->schema([
                            TextInput::make('from')
                                ->label(__('filament.filters.base.from'))
                                ->integer(),
    
                            TextInput::make('to')
                                ->label(__('filament.filters.base.to'))
                                ->integer(),
                        ])
                ]),
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
                Arr::get($data, 'from'),
                fn (Builder $query, $value): Builder => $query->where($this->getName(), ComparisonOperator::GTE->value, $value),
            )
            ->when(
                Arr::get($data, 'to'),
                fn (Builder $query, $value): Builder => $query->where($this->getName(), ComparisonOperator::LTE->value, $value),
            );
    }
}