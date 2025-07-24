<?php

declare(strict_types=1);

namespace App\Filament\Components\Filters;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Filament\Components\Fields\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class NumberFilter extends Filter
{
    /**
     * Get the schema components for the filter.
     *
     * @return Component[]
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
                            TextInput::make('from')
                                ->label(__('filament.filters.base.from'))
                                ->integer(),

                            TextInput::make('to')
                                ->label(__('filament.filters.base.to'))
                                ->integer(),
                        ]),
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
