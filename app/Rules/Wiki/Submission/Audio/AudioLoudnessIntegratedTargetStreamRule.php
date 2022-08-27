<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Audio;

use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

/**
 * Class AudioLoudnessIntegratedTargetStreamRule.
 */
class AudioLoudnessIntegratedTargetStreamRule extends SubmissionRule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  UploadedFile  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $loudness = $this->loudness();

        $target = floatval(Arr::get($loudness, 'input_i'));

        return $target >= -17.0 && $target <= -14.0;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.submission.audio_loudness_integrated_target');
    }
}
