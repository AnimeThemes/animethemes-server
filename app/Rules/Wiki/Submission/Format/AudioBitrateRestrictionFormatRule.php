<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Rules\Wiki\Submission\SubmissionRule;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class AudioBitrateRestrictionFormatRule.
 */
class AudioBitrateRestrictionFormatRule extends SubmissionRule
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
        $format = $this->format();

        $bitrate = intval(Arr::get($format, 'bit_rate'));

        if ($bitrate < 128000 || $bitrate > 360000) {
            $fail(__('validation.submission.format_bitrate_restriction'));
        }
    }
}
