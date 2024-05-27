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
    protected string $attribute = '';

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
     * Get the form for the filter.
     *
     * @return array
     */
    public function getFormSchema(): array
    {
        return [
            TextInput::make($this->attribute)
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
        return Arr::get($data, $this->attribute) !== null
            ? $query->where($this->attribute, Arr::get($data, $this->attribute))
            : $query;
    }
}