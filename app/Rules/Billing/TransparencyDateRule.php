<?php

declare(strict_types=1);

namespace App\Rules\Billing;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class TransparencyDateRule.
 */
readonly class TransparencyDateRule implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param  Collection<int, Carbon>  $validDates
     * @return void
     */
    public function __construct(protected Collection $validDates)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->validDates->contains(fn (Carbon $validDate) => $validDate->format(AllowedDateFormat::YM->value) === $value)) {
            $fail(__('The selected month is not valid.'));
        }
    }
}
