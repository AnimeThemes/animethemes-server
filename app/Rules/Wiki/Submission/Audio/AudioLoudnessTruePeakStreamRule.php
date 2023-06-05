<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Audio;

use App\Rules\Wiki\Submission\SubmissionRule;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class AudioLoudnessTruePeakStreamRule.
 */
class AudioLoudnessTruePeakStreamRule extends SubmissionRule
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
        $loudness = $this->loudness();

        $peak = floatval(Arr::get($loudness, 'input_tp'));

        if ($peak > 0.0) {
            $fail(__('validation.submission.audio_loudness_true_peak'));
        }
    }
}
