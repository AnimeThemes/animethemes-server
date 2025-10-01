<?php

declare(strict_types=1);

namespace App\Filament\Components\Filters;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class DateFilter extends Filter
{
    protected string $fromLabel = 'From';
    protected string $toLabel = 'To';

    public function labels(string $fromLabel, string $toLabel): static
    {
        $this->fromLabel = $fromLabel;
        $this->toLabel = $toLabel;

        return $this;
    }

    /**
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
                            DatePicker::make($this->getName().'_'.'from')
                                ->label($this->fromLabel)
                                ->native(false)
                                ->required(),
                            DatePicker::make($this->getName().'_'.'to')
                                ->label($this->toLabel)
                                ->native(false)
                                ->required(),
                        ]),
                ]),
        ];
    }

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
