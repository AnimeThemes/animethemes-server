<?php

declare(strict_types=1);

namespace App\Filament\Components\Filters;

use App\Filament\Components\Fields\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class TextFilter extends Filter
{
    /**
     * @return Component[]
     */
    public function getSchemaComponents(): array
    {
        return [
            TextInput::make($this->getName())
                ->label($this->label),
        ];
    }

    /**
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
