<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Audio;

use App\Rules\Wiki\Submission\SubmissionRule;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class AudioChannelLayoutStreamRule.
 */
class AudioChannelLayoutStreamRule extends SubmissionRule
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
        $audio = Arr::first(
            $this->streams(),
            fn (array $stream) => Arr::get($stream, 'codec_type') === 'audio'
        );

        if (Arr::get($audio, 'channel_layout') !== 'stereo') {
            $fail(__('validation.submission.audio_channel_layout'));
        }
    }
}
