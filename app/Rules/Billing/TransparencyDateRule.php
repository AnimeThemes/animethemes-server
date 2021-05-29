<?php

namespace App\Rules\Billing;

use App\Enums\Filter\AllowedDateFormat;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;

class TransparencyDateRule implements Rule
{
    /**
     * The name of the rule.
     */
    protected $rule = 'transparency_date';

    /**
     * The list of valid transparency dates.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $validDates;

    /**
     * Create a new rule instance.
     *
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
    public function passes($attribute, $value)
    {
        return $this->validDates->contains(function ($item) use ($value) {
            return $item->format(AllowedDateFormat::WITH_MONTH) === $value;
        });
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('The selected month is not valid.');
    }
}
