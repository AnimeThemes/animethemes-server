<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Audio;

use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Http\UploadedFile;

/**
 * Class AudioCodecStreamRule.
 */
class AudioCodecStreamRule extends SubmissionRule
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

        return $audio !== null && $audio->get('codec_name') === 'opus';
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.submission.audio_codec');
    }
}
