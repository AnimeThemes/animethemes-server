<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Rules\Wiki\Submission\SubmissionRule;
use Closure;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class ExtraneousChaptersFormatRule.
 */
class ExtraneousChaptersFormatRule extends SubmissionRule
{
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
        if (! empty($this->chapters())) {
            $fail(__('validation.submission.format_extraneous_chapters'));
        }
    }
}
