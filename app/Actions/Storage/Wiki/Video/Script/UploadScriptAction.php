<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Base\UploadAction;
use App\Constants\Config\VideoConstants;
use App\Contracts\Actions\Storage\StorageResults;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

/**
 * Class UploadScriptAction.
 */
class UploadScriptAction extends UploadAction
{
    /**
     * Create a new action instance.
     *
     * @param  UploadedFile  $file
     * @param  string  $path
     * @param  Video|null  $video
     */
    public function __construct(UploadedFile $file, string $path, protected ?Video $video = null)
    {
        parent::__construct($file, $path);
    }

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

        $this->getOrCreateScript();
    }

    /**
     * Get existing or create new script for file upload.
     *
     * @return VideoScript
     */
    protected function getOrCreateScript(): VideoScript
    {
        $path = Str::of($this->path)
            ->finish(DIRECTORY_SEPARATOR)
            ->append($this->file->getClientOriginalName())
            ->__toString();

        $attributes = [
            VideoScript::ATTRIBUTE_PATH => $path,
        ];

        if ($this->video !== null) {
            $attributes[VideoScript::ATTRIBUTE_VIDEO] = $this->video->getKey();
        }

        return VideoScript::updateOrCreate(
            [
                VideoScript::ATTRIBUTE_PATH => $path,
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
        return Arr::wrap(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));
    }
}
