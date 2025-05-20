<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Base\UploadAction;
use App\Actions\Storage\Wiki\Video\Script\UploadScriptAction;
use App\Constants\Config\VideoConstants;
use App\Contracts\Actions\Storage\StorageResults;
use App\Enums\Models\User\EncodeType;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Auth\User;
use App\Models\User\Encode;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Rules\Wiki\Submission\SubmissionRule;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
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
     * @param  User|null  $encoder
     */
    public function __construct(
        UploadedFile $file,
        string $path,
        protected array $attributes = [],
        protected ?AnimeThemeEntry $entry = null,
        protected readonly ?UploadedFile $script = null,
        protected ?User $encoder = null,
    ) {
        parent::__construct($file, $path);
    }

    /**
     * Processes to be completed after handling action.
     *
     * @param  StorageResults  $storageResults
     * @return Video|null
     *
     * @throws Exception
     */
    public function then(StorageResults $storageResults): ?Video
    {
        if ($storageResults->toActionResult()->hasFailed()) {
            return null;
        }

        try {
            DB::beginTransaction();

            $video = $this->getOrCreateVideo();

            $this->attachEntry($video);

            $this->uploadScript($video);

            $this->markEncode($video);

            DB::commit();

            return $video;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }
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
            Video::ATTRIBUTE_RESOLUTION => intval(Arr::get(SubmissionRule::$ffprobeData['streams'][0], 'height')),
            Video::ATTRIBUTE_SIZE => $this->file->getSize(),
        ];

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
     * Mark the encoder for the video.
     *
     * @param  Video  $video
     * @return void
     */
    protected function markEncode(Video $video): void
    {
        if ($encoder = $this->encoder) {
            // Mark any existing encodes as old if the video is being replaced.
            Encode::query()
                ->whereBelongsTo($video, Encode::RELATION_VIDEO)
                ->where(Encode::ATTRIBUTE_TYPE, EncodeType::CURRENT->value)
                ->update([Encode::ATTRIBUTE_TYPE => EncodeType::OLD->value]);

            Encode::query()->create([
                Encode::ATTRIBUTE_TYPE => EncodeType::CURRENT->value,
                Encode::ATTRIBUTE_USER => $encoder->getKey(),
                Encode::ATTRIBUTE_VIDEO => $video->getKey(),
            ]);
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
