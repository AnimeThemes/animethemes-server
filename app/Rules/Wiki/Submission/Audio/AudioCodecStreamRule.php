<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Audio;

use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\SubmissionRule;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Translation\PotentiallyTranslatedString;
use Laravel\Pennant\Feature;

class AudioCodecStreamRule extends SubmissionRule
{
    /**
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Feature::for(null)->active(FeatureConstants::IGNORE_ALL_FILE_VALIDATIONS)) {
            return;
        }

        $audio = Arr::first(
            $this->streams(),
            fn (array $stream): bool => Arr::get($stream, 'codec_type') === 'audio'
        );

        if (Arr::get($audio, 'codec_name') !== 'opus') {
            $fail(__('validation.submission.audio_codec'));
        }
    }
}
