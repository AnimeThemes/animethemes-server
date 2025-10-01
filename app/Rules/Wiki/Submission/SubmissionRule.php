<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission;

use App\Actions\Storage\Wiki\UploadedFileAction;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

abstract class SubmissionRule implements ValidationRule, ValidatorAwareRule
{
    /**
     * The FFmpeg/FFprobe action to the uploaded file.
     */
    protected UploadedFileAction $uploadedFileAction;

    /**
     * Set the current validator.
     */
    public function setValidator(Validator $validator): self
    {
        /** @var UploadedFile|array $file */
        $file = Arr::get($validator->getData(), 'file');

        if (is_array($file)) {
            $file = Arr::first($file);
        }

        $uploadedFileAction = $validator->getValue('uploadedFileAction');
        if ($file !== null && $uploadedFileAction === null) {
            $uploadedFileAction = new UploadedFileAction($file);

            $uploadedFileAction->setLoudnessStats();

            $validator->setValue('uploadedFileAction', $uploadedFileAction);
        }
        $this->uploadedFileAction = $uploadedFileAction;

        return $this;
    }

    /**
     * Get submission streams.
     */
    protected function streams(): array
    {
        return $this->uploadedFileAction->streams();
    }

    /**
     * Get submission format.
     */
    protected function format(): array
    {
        return $this->uploadedFileAction->format();
    }

    /**
     * Get submission chapters.
     */
    protected function chapters(): array
    {
        return $this->uploadedFileAction->chapters();
    }

    /**
     * For WebMs, tags will be contained in the format object.
     * For Audios, tags will be contained in the stream object.
     */
    protected function tags(): array
    {
        return $this->uploadedFileAction->tags();
    }

    /**
     * Get the submission loudness stats.
     */
    protected function loudness(): array
    {
        return $this->uploadedFileAction->loudness();
    }
}
