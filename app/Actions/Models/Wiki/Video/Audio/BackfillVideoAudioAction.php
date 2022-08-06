<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Video\Audio;

use App\Actions\Models\Wiki\BackfillAudioAction;
use App\Actions\Repositories\ReconcileResults;
use App\Actions\Repositories\Wiki\Audio\ReconcileAudioRepositories;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

/**
 * Class BackfillVideoAudioAction.
 *
 * @extends BackfillAudioAction<Video>
 */
class BackfillVideoAudioAction extends BackfillAudioAction
{
    /**
     * Create a new action instance.
     *
     * @param  Video  $video
     */
    public function __construct(Video $video)
    {
        parent::__construct($video);
    }

    /**
     * Get the model the action is handling.
     *
     * @return Video
     */
    public function getModel(): Video
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
     * Get or Create Audio.
     *
     * @return Audio|null
     */
    protected function getAudio(): ?Audio
    {
        // It's possible that the video is not attached to any themes, exit early.
        $sourceVideo = $this->getSourceVideo();
        if ($sourceVideo === null) {
            return null;
        }

        // First, attempt to set audio from the source video
        $audio = $sourceVideo->audio;

        // Second, attempt to set audio from path
        $audioPath = Str::replace('webm', 'ogg', $sourceVideo->path);
        if ($audio === null) {
            $audio = Audio::query()->firstWhere(Audio::ATTRIBUTE_PATH, $audioPath);
        }

        // Finally, extract audio from the source video
        if ($audio === null) {
            Log::info("Extracting Audio from Video '{$sourceVideo->getName()}'");

            $this->extractAudio($sourceVideo, $audioPath);
            $results = $this->reconcileAudio();
            $results->toLog();
            $audio = $results->getCreated()->firstWhere(fn (Audio $audio) => $audio->path === $audioPath);
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
            FFMpeg::fromDisk(Config::get('video.disk'))
                ->open($video->path)
                ->addFilter(new AudioClipFilter(new TimeCode(0, 0, 0, 0)))
                ->addFilter(new AddMetadataFilter())
                ->export()
                ->toDisk(Config::get('audio.disk'))
                ->save($audioPath);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        } finally {
            FFMpeg::cleanupTemporaryFiles();
        }
    }

    /**
     * Reconcile audio repositories.
     *
     * @return ReconcileResults
     */
    protected function reconcileAudio(): ReconcileResults
    {
        $action = new ReconcileAudioRepositories();

        $sourceRepository = App::make(AudioSourceRepository::class);

        $destinationRepository = App::make(AudioDestinationRepository::class);

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
        Log::info("Associating Audio '{$audio->getName()}' with Video '{$this->getModel()->getName()}'");
        $this->relation()->associate($audio)->save();
    }
}
