<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Video;

use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\SubmissionRule;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Translation\PotentiallyTranslatedString;
use Laravel\Pennant\Feature;

/**
 * Class VideoIndexStreamRule.
 */
class VideoIndexStreamRule extends SubmissionRule
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

        $stream = Arr::first(
            $this->streams(),
            fn (array $stream) => Arr::get($stream, 'codec_type') === 'video' && Arr::get($stream, 'index') === 0
        );

        if ($stream === null) {
            $fail(__('validation.submission.video_index'));
        }
    }
}
