<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Base\UploadAction;
use App\Actions\Storage\Wiki\Video\Script\UploadScriptAction;
use App\Constants\Config\VideoConstants;
use App\Contracts\Actions\Storage\StorageResults;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Class UploadVideoAction.
 */
class UploadVideoAction extends UploadAction
{
    /**
     * Create a new action instance.
     *
     * @param  UploadedFile  $file
     * @param  string  $path
     * @param  array  $attributes
     * @param  AnimeThemeEntry|null  $entry
     * @param  UploadedFile|null  $script
     */
    public function __construct(
        UploadedFile $file,
        string $path,
        protected array $attributes = [],
        protected ?AnimeThemeEntry $entry = null,
        protected readonly ?UploadedFile $script = null
    ) {
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

        $video = $this->getOrCreateVideo();

        $this->attachEntry($video);

        $this->uploadScript($video);
    }

    /**
     * Get existing or create new video for file upload.
     *
     * @return Video
     */
    protected function getOrCreateVideo(): Video
    {
        $path = Str::of($this->path)
            ->finish(DIRECTORY_SEPARATOR)
            ->append($this->file->getClientOriginalName())
            ->__toString();

        $attributes = [
            Video::ATTRIBUTE_FILENAME => File::name($this->file->getClientOriginalName()),
            Video::ATTRIBUTE_MIMETYPE => $this->file->getMimeType(),
            Video::ATTRIBUTE_PATH => $path,
            Video::ATTRIBUTE_SIZE => $this->file->getSize(),
        ];

        if (Arr::has($this->attributes, Video::ATTRIBUTE_RESOLUTION)) {
            $attributes[Video::ATTRIBUTE_RESOLUTION] = Arr::get($this->attributes, Video::ATTRIBUTE_RESOLUTION);
        }
        if (Arr::has($this->attributes, Video::ATTRIBUTE_NC)) {
            $attributes[Video::ATTRIBUTE_NC] = Arr::get($this->attributes, Video::ATTRIBUTE_NC);
        }
        if (Arr::has($this->attributes, Video::ATTRIBUTE_SUBBED)) {
            $attributes[Video::ATTRIBUTE_SUBBED] = Arr::get($this->attributes, Video::ATTRIBUTE_SUBBED);
        }
        if (Arr::has($this->attributes, Video::ATTRIBUTE_LYRICS)) {
            $attributes[Video::ATTRIBUTE_LYRICS] = Arr::get($this->attributes, Video::ATTRIBUTE_LYRICS);
        }
        if (Arr::has($this->attributes, Video::ATTRIBUTE_UNCEN)) {
            $attributes[Video::ATTRIBUTE_UNCEN] = Arr::get($this->attributes, Video::ATTRIBUTE_UNCEN);
        }
        if (Arr::has($this->attributes, Video::ATTRIBUTE_UNCEN)) {
            $overlap = VideoOverlap::unstrictCoerce(Arr::get($this->attributes, Video::ATTRIBUTE_OVERLAP));
            if ($overlap !== null) {
                $attributes[Video::ATTRIBUTE_OVERLAP] = $overlap;
            }
        }
        if (Arr::has($this->attributes, Video::ATTRIBUTE_SOURCE)) {
            $attributes[Video::ATTRIBUTE_SOURCE] = VideoSource::unstrictCoerce(Arr::get($this->attributes, Video::ATTRIBUTE_SOURCE));
        }

        return Video::updateOrCreate(
            [
                Video::ATTRIBUTE_BASENAME => $this->file->getClientOriginalName(),
            ],
            $attributes
        );
    }

    /**
     * Attach entry to created video if uploaded from entry detail screen.
     *
     * @param  Video  $video
     * @return void
     */
    protected function attachEntry(Video $video): void
    {
        if ($this->entry !== null && $video->wasRecentlyCreated) {
            $video->animethemeentries()->attach($this->entry);
        }
    }

    /**
     * Upload & Associate Script if video upload was successful.
     *
     * @param  Video  $video
     * @return void
     */
    protected function uploadScript(Video $video): void
    {
        if ($this->script !== null) {
            $uploadScript = new UploadScriptAction($this->script, $this->path, $video);

            $scriptResult = $uploadScript->handle();

            $uploadScript->then($scriptResult);
        }
    }

    /**
     * The list of disk names.
     *
     * @return array
     */
    public function disks(): array
    {
        return Config::get(VideoConstants::DISKS_QUALIFIED);
    }
}
