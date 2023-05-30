<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Audio;

use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

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
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  UploadedFile  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $stream = Arr::first(
            $this->streams(),
            fn (array $stream) => Arr::get($stream, 'codec_type') === 'audio' && Arr::get($stream, 'index') === $this->expected
        );

        return $stream !== null;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.submission.audio_index');
    }
}
