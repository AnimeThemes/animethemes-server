<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Rules\Wiki\Submission\SubmissionRule;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class EncoderNameFormatRule.
 */
class EncoderNameFormatRule extends SubmissionRule
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
        $tags = $this->tags();

        $encoder = Arr::get($tags, 'encoder');

        if (! Str::startsWith($encoder, 'Lavf')) {
            $fail(__('validation.submission.format_encoder_name'));
        }
    }
}
