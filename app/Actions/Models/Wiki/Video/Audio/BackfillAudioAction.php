<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Video\Audio;

use App\Actions\ActionResult;
use App\Actions\Models\BackfillAction;
use App\Actions\Storage\Wiki\Audio\MoveAudioAction;
use App\Actions\Storage\Wiki\Audio\UploadAudioAction;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\ActionStatus;
use App\Enums\Actions\Models\Wiki\Video\DeriveSourceVideo;
use App\Enums\Actions\Models\Wiki\Video\OverwriteAudio;
use App\Enums\Actions\Models\Wiki\Video\ReplaceRelatedAudio;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends BackfillAction<Video>
 */
class BackfillAudioAction extends BackfillAction
{
    public function __construct(
        Video $video,
        protected readonly DeriveSourceVideo $deriveSourceVideo = DeriveSourceVideo::YES,
        protected readonly OverwriteAudio $overwriteAudio = OverwriteAudio::NO,
        protected readonly ReplaceRelatedAudio $replaceRelatedAudio = ReplaceRelatedAudio::NO,
    ) {
        parent::__construct($video);
    }

    /**
     * @throws Exception
     */
    public function handle(): ActionResult
    {
        try {
            DB::beginTransaction();

            if ($this->relation()->getQuery()->exists() && ! $this->overwriteAudio()) {
                DB::rollBack();
                Log::info("{$this->label()} '{$this->getModel()->getName()}' already has Audio'.");

                return new ActionResult(ActionStatus::SKIPPED);
            }

            $audio = $this->getAudio();

            if ($audio !== null) {
                $this->attachAudio($audio);
            }

            if ($this->relation()->getQuery()->doesntExist()) {
                DB::rollBack();

                return new ActionResult(
                    ActionStatus::FAILED,
                    "{$this->label()} '{$this->getModel()->getName()}' has no Audio after backfilling. Please review."
                );
            }

            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            throw $e;
        }

        return new ActionResult(ActionStatus::PASSED);
    }

    /**
     * Get the model the action is handling.
     */
    protected function getModel(): Video
    {
        return $this->model;
    }

    /**
     * Get the relation to audio.
     */
    protected function relation(): BelongsTo
    {
        return $this->getModel()->audio();
    }

    protected function deriveSourceVideo(): bool
    {
        return $this->deriveSourceVideo === DeriveSourceVideo::YES;
    }

    protected function overwriteAudio(): bool
    {
        return $this->overwriteAudio === OverwriteAudio::YES;
    }

    protected function replaceRelatedAudio(): bool
    {
        return $this->replaceRelatedAudio === ReplaceRelatedAudio::YES;
    }

    /**
     * Get or Create Audio.
     *
     * @throws Exception
     */
    protected function getAudio(): ?Audio
    {
        // Allow bypassing of source video derivation
        if ($this->replaceRelatedAudio()) {
            $sourceVideo = $this->getSourceVideo('<');
        } elseif ($this->deriveSourceVideo()) {
            $sourceVideo = $this->getSourceVideo();
        } else {
            $sourceVideo = $this->getModel();
        }

        // It's possible that the video is not attached to any themes, exit early.
        if ($sourceVideo === null) {
            Log::error('Could not derive source video');

            return null;
        }

        // First, attempt to set audio from the source video
        $audio = $sourceVideo->audio;

        // When uploading a BD version we should get the parent audio of a WEB version and
        // move the file overwriting the content later. Therefore, the old model is not deleted.
        if ($this->replaceRelatedAudio() && $audio instanceof Audio) {
            $moveAction = new MoveAudioAction($audio, Str::replace('webm', 'ogg', $this->getModel()->path()));

            $storageResults = $moveAction->handle();

            $audio = $moveAction->then($storageResults);
        }

        // Anticipate audio path for FFmpeg save file
        $audioPath = $audio === null
            ? Str::replace('webm', 'ogg', $sourceVideo->path)
            : $audio->path;

        // Second, attempt to set audio from path
        if ($audio === null) {
            $audio = Audio::query()->firstWhere(Audio::ATTRIBUTE_PATH, $audioPath);
        }

        // Finally, extract audio from the source video
        if ($audio === null || $this->overwriteAudio() || $this->replaceRelatedAudio()) {
            if ($this->replaceRelatedAudio()) {
                $sourceVideo = $this->getModel();
            }

            return $this->extractAudio($sourceVideo);
        }

        return $audio;
    }

    protected function getSourceVideo(string $operation = '>'): ?Video
    {
        $source = null;

        $sourceCandidates = $this->getAdjacentVideos();

        foreach ($sourceCandidates as $sourceCandidate) {
            if ($operation === '>' && (! $source instanceof Video || $sourceCandidate->getSourcePriority() > $source->getSourcePriority())) {
                $source = $sourceCandidate;
            }

            if ($operation === '<' && (! $source instanceof Video || $sourceCandidate->getSourcePriority() < $source->getSourcePriority())) {
                $source = $sourceCandidate;
            }
        }

        return $source;
    }

    /**
     * @return Collection<int, Video>
     */
    protected function getAdjacentVideos(): Collection
    {
        $builder = AnimeTheme::query();

        $sortRelation = $builder->getRelation(AnimeTheme::RELATION_ANIME);

        $orderByNameQuery = $sortRelation->getRelationExistenceQuery($sortRelation->getQuery(), $builder, [Anime::ATTRIBUTE_NAME]);
        $orderBySeasonQuery = $sortRelation->getRelationExistenceQuery($sortRelation->getQuery(), $builder, [Anime::ATTRIBUTE_SEASON]);
        $orderByYearQuery = $sortRelation->getRelationExistenceQuery($sortRelation->getQuery(), $builder, [Anime::ATTRIBUTE_YEAR]);
        $orderByMediaFormatQuery = $sortRelation->getRelationExistenceQuery($sortRelation->getQuery(), $builder, [Anime::ATTRIBUTE_MEDIA_FORMAT]);

        return $builder->whereHas(AnimeTheme::RELATION_VIDEOS, fn (Builder $relationBuilder) => $relationBuilder->whereKey($this->getModel()))
            ->orderBy($orderByMediaFormatQuery->toBase())
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
     */
    protected function extractAudio(Video $video): ?Audio
    {
        $audioBasename = Str::replace('webm', 'ogg', $video->basename);
        $audioPath = Storage::disk('local')->path($audioBasename);

        try {
            Storage::disk('local')->put(
                $video->basename,
                Storage::disk(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED))->get($video->path)
            );

            Process::run([
                'ffmpeg',
                '-v',
                'quiet',
                '-i',
                Storage::disk('local')->path($video->basename),
                '-vn',
                '-acodec',
                'copy',
                '-f',
                'ogg',
                '-y',
                $audioPath,
            ])
                ->throw();

            $uploadAudio = new UploadAudioAction(
                new UploadedFile($audioPath, $audioBasename),
                File::dirname($video->path)
            );

            $storageResults = $uploadAudio->handle();

            return $uploadAudio->then($storageResults);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        } finally {
            Storage::disk('local')->delete([$video->basename, $audioBasename]);
        }

        return null;
    }

    protected function attachAudio(Audio $audio): void
    {
        if ($this->relation()->isNot($audio)) {
            Log::info("Associating Audio '{$audio->getName()}' with Video '{$this->getModel()->getName()}'");
            $this->relation()->associate($audio)->save();
        }
    }
}
