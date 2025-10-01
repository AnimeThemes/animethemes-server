<?php

declare(strict_types=1);

namespace App\Filament\Components\Filters;

use App\Enums\Http\Api\Filter\ComparisonOperator;
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

    public function applyToBaseQuery(Builder $query, array $data = []): Builder
    {
        return $query->when(
            filled($text = Arr::get($data, $this->getName())),
            fn (Builder $builder) => $builder->where($this->getName(), ComparisonOperator::LIKE->value, "%$text%")
        );
    }
}
