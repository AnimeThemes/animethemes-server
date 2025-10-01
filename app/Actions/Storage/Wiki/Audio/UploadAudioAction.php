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

class UploadAudioAction extends UploadAction
{
    /**
     * Processes to be completed after handling action.
     */
    public function then(StorageResults $storageResults): ?Audio
    {
        if ($storageResults->toActionResult()->hasFailed()) {
            return null;
        }

        return $this->getOrCreateAudio();
    }

    /**
     * Get existing or create new audio for file upload.
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

        return Audio::query()->updateOrCreate([
            Audio::ATTRIBUTE_BASENAME => $this->file->getClientOriginalName(),
        ], $attributes);
    }

    /**
     * The list of disk names.
     */
    public function disks(): array
    {
        return Config::get(AudioConstants::DISKS_QUALIFIED);
    }
}
