<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Rules\Wiki\Submission\SubmissionRule;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class VideoBitrateRestrictionFormatRule.
 */
class VideoBitrateRestrictionFormatRule extends SubmissionRule
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

        $video = Arr::first(
            $this->streams(),
            fn (array $stream) => Arr::get($stream, 'codec_type') === 'video'
        );

        $bitrate = intval(Arr::get($format, 'bit_rate'));
        $height = intval(Arr::get($video, 'height'));

        // Linear approximation of egregious bitrate by resolution
        if ($bitrate > $height * 7100 + 475000) {
            $fail(__('validation.submission.format_bitrate_restriction'));
        }
    }
}
