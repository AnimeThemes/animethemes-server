<?php

declare(strict_types=1);

namespace App\Filament\Components\Filters;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * Class TextFilter.
 */
class TextFilter extends Filter
{
    /**
     * Get the form for the filter.
     *
     * @return array
     */
    public function getFormSchema(): array
    {
        return [
            TextInput::make($this->getName())
                ->label($this->label),
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
        return Arr::get($data, $this->getName()) !== null
            ? $query->where($this->getName(), Arr::get($data, $this->getName()))
            : $query;
    }
}