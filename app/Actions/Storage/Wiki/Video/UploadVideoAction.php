<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Video;

use App\Actions\Repositories\ReconcileResults;
use App\Actions\Storage\Base\UploadAction;
use App\Actions\Storage\Wiki\Video\Script\UploadScriptAction;
use App\Concerns\Repositories\Wiki\ReconcilesVideoRepositories;
use App\Constants\Config\VideoConstants;
use App\Contracts\Actions\Storage\StorageResults;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

/**
 * Class UploadVideoAction.
 */
class UploadVideoAction extends UploadAction
{
    use ReconcilesVideoRepositories;

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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function then(StorageResults $storageResults): void
    {
        $reconcileResults = $this->reconcileRepositories();

        $reconcileResults->toLog();

        // The video was successfully uploaded and reconciled into the database, so we can attempt further actions
        if ($reconcileResults instanceof ReconcileResults) {
            $this->setAttributes($reconcileResults);
            $this->attachEntry($reconcileResults);
            $this->uploadScript($reconcileResults);
        }
    }

    /**
     * Set additional video attributes not provided by reconciliation.
     *
     * @param  ReconcileResults  $reconcileResults
     * @return void
     */
    protected function setAttributes(ReconcileResults $reconcileResults): void
    {
        $video = $reconcileResults->getCreated()->firstWhere(Video::ATTRIBUTE_BASENAME, $this->file->getClientOriginalName());

        if ($video === null) {
            $video = $reconcileResults->getUpdated()->firstWhere(Video::ATTRIBUTE_BASENAME, $this->file->getClientOriginalName());
        }

        if ($video instanceof Video && ! empty($this->attributes)) {
            if (Arr::has($this->attributes, Video::ATTRIBUTE_RESOLUTION)) {
                $video->resolution = Arr::get($this->attributes, Video::ATTRIBUTE_RESOLUTION);
            }
            if (Arr::has($this->attributes, Video::ATTRIBUTE_NC)) {
                $video->nc = Arr::get($this->attributes, Video::ATTRIBUTE_NC);
            }
            if (Arr::has($this->attributes, Video::ATTRIBUTE_SUBBED)) {
                $video->subbed = Arr::get($this->attributes, Video::ATTRIBUTE_SUBBED);
            }
            if (Arr::has($this->attributes, Video::ATTRIBUTE_LYRICS)) {
                $video->lyrics = Arr::get($this->attributes, Video::ATTRIBUTE_LYRICS);
            }
            if (Arr::has($this->attributes, Video::ATTRIBUTE_UNCEN)) {
                $video->uncen = Arr::get($this->attributes, Video::ATTRIBUTE_UNCEN);
            }
            if (Arr::has($this->attributes, Video::ATTRIBUTE_UNCEN)) {
                $overlap = VideoOverlap::unstrictCoerce(Arr::get($this->attributes, Video::ATTRIBUTE_OVERLAP));
                if ($overlap !== null) {
                    $video->overlap = $overlap;
                }
            }
            if (Arr::has($this->attributes, Video::ATTRIBUTE_SOURCE)) {
                $video->source = VideoSource::unstrictCoerce(Arr::get($this->attributes, Video::ATTRIBUTE_SOURCE));
            }

            if ($video->isDirty()) {
                $video->save();
            }
        }
    }

    /**
     * Attach entry to created video if uploaded from entry detail screen.
     *
     * @param  ReconcileResults  $reconcileResults
     * @return void
     */
    protected function attachEntry(ReconcileResults $reconcileResults): void
    {
        $video = $reconcileResults->getCreated()->firstWhere(Video::ATTRIBUTE_BASENAME, $this->file->getClientOriginalName());

        if ($video instanceof Video && $this->entry !== null) {
            $video->animethemeentries()->attach($this->entry);
        }
    }

    /**
     * Upload & Associate Script if video upload was successful.
     *
     * @param  ReconcileResults  $reconcileResults
     * @return void
     */
    protected function uploadScript(ReconcileResults $reconcileResults): void
    {
        $video = $reconcileResults->getCreated()->firstWhere(Video::ATTRIBUTE_BASENAME, $this->file->getClientOriginalName());

        if ($video === null) {
            $video = $reconcileResults->getUpdated()->firstWhere(Video::ATTRIBUTE_BASENAME, $this->file->getClientOriginalName());
        }

        if ($video instanceof Video && $this->script !== null) {
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
