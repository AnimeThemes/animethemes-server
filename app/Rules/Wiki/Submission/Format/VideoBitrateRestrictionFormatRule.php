<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\SubmissionRule;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Translation\PotentiallyTranslatedString;
use Laravel\Pennant\Feature;

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
        if (Feature::for(null)->active(FeatureConstants::IGNORE_ALL_FILE_VALIDATIONS)) return;

        $format = $this->format();

        $video = Arr::first(
            $this->streams(),
            fn (array $stream) => Arr::get($stream, 'codec_type') === 'video'
        );

        $bitrate = intval(Arr::get($format, 'bit_rate'));
        $height = intval(Arr::get($video, 'height'));

        // Linear approximation of egregious bitrate by resolution
        if (Feature::for(null)->active(FeatureConstants::VIDEO_BITRATE_RESTRICTION) && $bitrate > $height * 7100 + 475000) {
            $fail(__('validation.submission.format_bitrate_restriction'));
        }
    }
}
