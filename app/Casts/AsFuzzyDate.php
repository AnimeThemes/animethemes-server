<?php

declare(strict_types=1);

namespace App\Casts;

use App\ValueObjects\FuzzyDate;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class AsFuzzyDate implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?FuzzyDate
    {
        return FuzzyDate::fromString($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (blank($value) || $value === '00000000') {
            return null;
        }

        if ($value instanceof FuzzyDate) {
            return $value->toString();
        }

        if (is_string($value)) {
            return FuzzyDate::fromString($value)?->toString();
        }

        if (is_array($value)) {
            return new FuzzyDate(
                (int) Arr::get($value, 'year') ?: null,
                (int) Arr::get($value, 'month') ?: null,
                (int) Arr::get($value, 'day') ?: null,
            )->toString();
        }

        throw new InvalidArgumentException('Invalid FuzzyDate value.');
    }
}
