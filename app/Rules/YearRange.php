<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class YearRange implements Rule
{

    const MIN_YEAR = 1960;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        try {
            return YearRange::min() <= $value && $value <= YearRange::max();
        } catch (Exception $exception) {
            Log::error($exception);
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Year should be between ' . YearRange::min() . ' and ' . YearRange::max();
    }

    public static function min() {
        return self::MIN_YEAR;
    }

    public static function max() {
        return date('Y') + 1;
    }
}
