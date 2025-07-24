<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Base\UploadAction;
use App\Actions\Storage\Wiki\UploadedFileAction;
use App\Actions\Storage\Wiki\Video\Script\UploadScriptAction;
use App\Constants\Config\VideoConstants;
use App\Contracts\Actions\Storage\StorageResults;
use App\Enums\Models\User\EncodeType;
use App\Models\Auth\User;
use App\Models\User\Encode;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use BackedEnum;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UploadVideoAction extends UploadAction
{
    /**
     * @param  array  $attributes
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
            Video::ATTRIBUTE_RESOLUTION => new UploadedFileAction($this->file)->resolution(),
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
        if (Arr::has($this->attributes, Video::ATTRIBUTE_OVERLAP)) {
            $overlap = Arr::get($this->attributes, Video::ATTRIBUTE_OVERLAP);
            $attributes[Video::ATTRIBUTE_OVERLAP] = $overlap instanceof BackedEnum ? $overlap->value : $overlap;
        }
        if (Arr::has($this->attributes, Video::ATTRIBUTE_SOURCE)) {
            $source = Arr::get($this->attributes, Video::ATTRIBUTE_SOURCE);
            $attributes[Video::ATTRIBUTE_SOURCE] = $source instanceof BackedEnum ? $source->value : $source;
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
     */
    protected function attachEntry(Video $video): void
    {
        if ($this->entry !== null && $video->wasRecentlyCreated) {
            $video->animethemeentries()->attach($this->entry);
        }
    }

    /**
     * Upload & Associate Script if video upload was successful.
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
