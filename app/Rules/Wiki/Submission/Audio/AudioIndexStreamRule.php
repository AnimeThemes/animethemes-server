<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Audio;

use App\Constants\FeatureConstants;
use App\Models\Admin\Feature;
use App\Rules\Wiki\Submission\SubmissionRule;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class AudioIndexStreamRule.
 */
class AudioIndexStreamRule extends SubmissionRule
{
    /**
     * Create new rule instance.
     *
     * @param  int  $expected
     */
    public function __construct(protected readonly int $expected)
    {
    }

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
            fn (array $stream) => Arr::get($stream, 'codec_type') === 'audio' && Arr::get($stream, 'index') === $this->expected
        );

        if ($stream === null) {
            $fail(__('validation.submission.audio_index'));
        }
    }
}
