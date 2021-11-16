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
     * The name of the rule.
     */
    protected string $rule = 'transparency_date';

    /**
     * The list of valid transparency dates.
     *
     * @var Collection
     */
    protected Collection $validDates;

    /**
     * Create a new rule instance.
     *
     * @param  Collection  $validDates
     * @return void
     */
    public function __construct(Collection $validDates)
    {
        $this->validDates = $validDates;
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

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     *
     * @see \Illuminate\Validation\ValidationRuleParser::parseParameters
     */
    public function __toString()
    {
        return "{$this->rule}";
    }
}
