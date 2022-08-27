<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Audio;

use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Http\UploadedFile;

/**
 * Class AudioChannelsStreamRule.
 */
class AudioChannelsStreamRule extends SubmissionRule
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
        $audio = $this->streams()
            ->audios()
            ->first();

        return $audio !== null && $audio->get('channels') === 2;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.submission.audio_channels');
    }
}
