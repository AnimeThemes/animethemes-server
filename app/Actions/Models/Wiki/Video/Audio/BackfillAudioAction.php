<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Video\Audio;

use App\Actions\ActionResult;
use App\Actions\Models\BackfillAction;
use App\Actions\Repositories\ReconcileResults;
use App\Actions\Repositories\Wiki\Audio\ReconcileAudioRepositoriesAction;
use App\Constants\Config\AudioConstants;
use App\Constants\Config\VideoConstants;
use App\Contracts\Repositories\RepositoryInterface;
use App\Enums\Actions\ActionStatus;
use App\Enums\Actions\Models\Wiki\Video\DeriveSourceVideo;
use App\Enums\Actions\Models\Wiki\Video\OverwriteAudio;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use App\Repositories\Eloquent\Wiki\AudioRepository as AudioDestinationRepository;
use App\Repositories\Storage\Wiki\AudioRepository as AudioSourceRepository;
use Exception;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Filters\Audio\AddMetadataFilter;
use FFMpeg\Filters\Audio\AudioClipFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

/**
 * Class BackfillVideoAudioAction.
 *
 * @extends BackfillAction<Video>
 */
class BackfillAudioAction extends BackfillAction
{
    /**
     * Create a new action instance.
     *
     * @param  Video  $video
     * @param  DeriveSourceVideo  $deriveSourceVideo
     * @param  OverwriteAudio  $overwriteAudio
     */
    public function __construct(
        Video $video,
        protected readonly DeriveSourceVideo $deriveSourceVideo = new DeriveSourceVideo(DeriveSourceVideo::YES),
        protected readonly OverwriteAudio $overwriteAudio = new OverwriteAudio(OverwriteAudio::NO)
    ) {
        parent::__construct($video);
    }

    /**
     * Handle action.
     *
     * @return ActionResult
     *
     * @throws Exception
     */
    public function handle(): ActionResult
    {
        try {
            DB::beginTransaction();

            if ($this->relation()->getQuery()->exists() && ! $this->overwriteAudio()) {
                Log::info("{$this->label()} '{$this->getModel()->getName()}' already has Audio'.");

                return new ActionResult(ActionStatus::SKIPPED());
            }

            $audio = $this->getAudio();

            if ($audio !== null) {
                $this->attachAudio($audio);
            }

            if ($this->relation()->getQuery()->doesntExist()) {
                return new ActionResult(
                    ActionStatus::FAILED(),
                    "{$this->label()} '{$this->getModel()->getName()}' has no Audio after backfilling. Please review."
                );
            }

            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }

        return new ActionResult(ActionStatus::PASSED());
    }

    /**
     * Get the model the action is handling.
     *
     * @return Video
     */
    protected function getModel(): Video
    {
        return $this->model;
    }

    /**
     * Get the relation to audio.
     *
     * @return BelongsTo
     */
    protected function relation(): BelongsTo
    {
        return $this->getModel()->audio();
    }

    /**
     * Determine if the source video should be derived.
     *
     * @return bool
     */
    protected function deriveSourceVideo(): bool
    {
        return DeriveSourceVideo::YES()->is($this->deriveSourceVideo);
    }

    /**
     * Determine if audio should be overwritten.
     *
     * @return bool
     */
    protected function overwriteAudio(): bool
    {
        return OverwriteAudio::YES()->is($this->overwriteAudio);
    }

    /**
     * Get or Create Audio.
     *
     * @return Audio|null
     *
     * @throws Exception
     */
    protected function getAudio(): ?Audio
    {
        // Allow bypassing of source video derivation
        $sourceVideo = $this->deriveSourceVideo()
            ? $this->getSourceVideo()
            : $this->getModel();

        // It's possible that the video is not attached to any themes, exit early.
        if ($sourceVideo === null) {
            return null;
        }

        // First, attempt to set audio from the source video
        $audio = $sourceVideo->audio;

        // Anticipate audio path for FFmpeg save file
        $audioPath = $audio === null
            ? Str::replace('webm', 'ogg', $sourceVideo->path)
            : $audio->path;

        // Second, attempt to set audio from path
        if ($audio === null) {
            $audio = Audio::query()->firstWhere(Audio::ATTRIBUTE_PATH, $audioPath);
        }

        // Finally, extract audio from the source video
        if ($audio === null || $this->overwriteAudio()) {
            Log::info("Extracting Audio from Video '{$sourceVideo->getName()}'");

            $this->extractAudio($sourceVideo, $audioPath);
            $results = $this->reconcileAudio($audioPath);
            $results->toLog();

            if ($audio === null) {
                $audio = $results->getCreated()->firstWhere(fn (Audio $audio) => $audio->path === $audioPath);
            }
        }

        return $audio;
    }

    /**
     * Get the source video for the given video.
     *
     * @return Video|null
     */
    protected function getSourceVideo(): ?Video
    {
        $source = null;

        $sourceCandidates = $this->getAdjacentVideos();

        foreach ($sourceCandidates as $sourceCandidate) {
            if (! $source instanceof Video || $sourceCandidate->getSourcePriority() > $source->getSourcePriority()) {
                $source = $sourceCandidate;
            }
        }

        return $source;
    }

    /**
     * Get the adjacent videos for sourcing.
     *
     * @return Collection<int, Video>
     */
    protected function getAdjacentVideos(): Collection
    {
        $builder = AnimeTheme::query();

        $sortRelation = $builder->getRelation(AnimeTheme::RELATION_ANIME);

        $orderByNameQuery = $sortRelation->getRelationExistenceQuery($sortRelation->getQuery(), $builder, [Anime::ATTRIBUTE_NAME]);
        $orderBySeasonQuery = $sortRelation->getRelationExistenceQuery($sortRelation->getQuery(), $builder, [Anime::ATTRIBUTE_SEASON]);
        $orderByYearQuery = $sortRelation->getRelationExistenceQuery($sortRelation->getQuery(), $builder, [Anime::ATTRIBUTE_YEAR]);

        return $builder->whereHas(AnimeTheme::RELATION_VIDEOS, fn (Builder $relationBuilder) => $relationBuilder->whereKey($this->getModel()))
            ->orderBy($orderByYearQuery->toBase())
            ->orderBy($orderBySeasonQuery->toBase())
            ->orderBy($orderByNameQuery->toBase())
            ->with([
                AnimeTheme::RELATION_ANIME,
                AnimeTheme::RELATION_AUDIO,
                AnimeTheme::RELATION_ENTRIES => fn (Relation $relation) => $relation->getQuery()->orderBy(AnimeThemeEntry::ATTRIBUTE_VERSION),
            ])
            ->get()
            ->flatMap(fn (AnimeTheme $theme) => $theme->animethemeentries)
            ->flatMap(fn (AnimeThemeEntry $entry) => $entry->videos);
    }

    /**
     * Extract audio stream from video and store in filesystem.
     *
     * @param  Video  $video
     * @param  string  $audioPath
     * @return void
     */
    protected function extractAudio(Video $video, string $audioPath): void
    {
        try {
            foreach (Config::get(AudioConstants::DISKS_QUALIFIED, []) as $audioDisk) {
                FFMpeg::fromDisk(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED))
                    ->open($video->path)
                    ->addFilter(new AudioClipFilter(new TimeCode(0, 0, 0, 0)))
                    ->addFilter(new AddMetadataFilter())
                    ->export()
                    ->toDisk($audioDisk)
                    ->save($audioPath);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        } finally {
            FFMpeg::cleanupTemporaryFiles();
        }
    }

    /**
     * Reconcile audio repositories.
     *
     * @param  string  $audioPath
     * @return ReconcileResults
     *
     * @throws Exception
     */
    protected function reconcileAudio(string $audioPath): ReconcileResults
    {
        $action = new ReconcileAudioRepositoriesAction();

        /** @var RepositoryInterface $sourceRepository */
        $sourceRepository = App::make(AudioSourceRepository::class);
        $sourceRepository->handleFilter('path', File::dirname($audioPath));

        /** @var RepositoryInterface $destinationRepository */
        $destinationRepository = App::make(AudioDestinationRepository::class);
        $destinationRepository->handleFilter('path', File::dirname($audioPath));

        return $action->reconcileRepositories($sourceRepository, $destinationRepository);
    }

    /**
     * Attach Audio to model.
     *
     * @param  Audio  $audio
     * @return void
     */
    protected function attachAudio(Audio $audio): void
    {
        if ($this->relation()->isNot($audio)) {
            Log::info("Associating Audio '{$audio->getName()}' with Video '{$this->getModel()->getName()}'");
            $this->relation()->associate($audio)->save();
        }
    }
}
