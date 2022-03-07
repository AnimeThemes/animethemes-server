<?php

declare(strict_types=1);

namespace App\Rules\Billing;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;

/**
 * Class TransparencyDateRule.
 */
class TransparencyDateRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @param  Collection  $validDates
     * @return void
     */
    public function __construct(protected readonly Collection $validDates)
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return $this->validDates->contains(
            fn (Carbon $validDate) => $validDate->format(AllowedDateFormat::YM) === $value
        );
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('The selected month is not valid.');
    }
}
