<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Base\UploadAction;
use App\Constants\Config\AudioConstants;
use App\Contracts\Actions\Storage\StorageResults;
use App\Models\Wiki\Audio;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Class UploadAudioAction.
 */
class UploadAudioAction extends UploadAction
{
    /**
     * Processes to be completed after handling action.
     *
     * @param  StorageResults  $storageResults
     * @return void
     */
    public function then(StorageResults $storageResults): void
    {
        if ($storageResults->toActionResult()->hasFailed()) {
            return;
        }

        $this->getOrCreateAudio();
    }

    /**
     * Get existing or create new audio for file upload.
     *
     * @return Audio
     */
    protected function getOrCreateAudio(): Audio
    {
        $path = Str::of($this->path)
            ->finish(DIRECTORY_SEPARATOR)
            ->append($this->file->getClientOriginalName())
            ->__toString();

        $attributes = [
            Audio::ATTRIBUTE_FILENAME => File::name($this->file->getClientOriginalName()),
            Audio::ATTRIBUTE_MIMETYPE => $this->file->getMimeType(),
            Audio::ATTRIBUTE_PATH => $path,
            Audio::ATTRIBUTE_SIZE => $this->file->getSize(),
        ];

        return Audio::updateOrCreate(
            [
                Audio::ATTRIBUTE_BASENAME => $this->file->getClientOriginalName(),
            ],
            $attributes
        );
    }

    /**
     * The list of disk names.
     *
     * @return array
     */
    public function disks(): array
    {
        return Config::get(AudioConstants::DISKS_QUALIFIED);
    }
}
