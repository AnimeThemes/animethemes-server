<?php

declare(strict_types=1);

namespace App\Rules\GraphQL\Resolver;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class AnimeYearRule implements ValidationRule
{
    public function __construct(protected array $fieldSelection) {}

    /**
     * Restrict 'animes' field to a unique year.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            ($value === null || count($value) > 1)
            && (Arr::get($this->fieldSelection, 'season.anime') || Arr::get($this->fieldSelection, 'seasons.anime'))
        ) {
            $fail(__('validation.graphql.anime_year_query'));
        }
    }
}
