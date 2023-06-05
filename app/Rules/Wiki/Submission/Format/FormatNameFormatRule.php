<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Rules\Wiki\Submission\SubmissionRule;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class FormatNameFormatRule.
 */
class FormatNameFormatRule extends SubmissionRule
{
    /**
     * Create new rule instance.
     *
     * @param  string  $expected
     */
    public function __construct(protected readonly string $expected)
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
        $format = $this->format();

        $formatName = Arr::get($format, 'format_name');

        if ($formatName !== $this->expected) {
            $fail(__('validation.submission.format_format_name'));
        }
    }
}
