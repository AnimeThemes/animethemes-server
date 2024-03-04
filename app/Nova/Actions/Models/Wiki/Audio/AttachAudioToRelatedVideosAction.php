<?php

declare(strict_types=1);

namespace App\Nova\Actions\Models\Wiki\Audio;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class AttachAudioToRelatedVideosAction.
 */
class AttachAudioToRelatedVideosAction extends Action implements ShouldQueue
{
    /**
     * Create a new action instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.audio.attach_related_videos.name');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, Audio>  $models
     * @return Collection
     */
    public function handle(ActionFields $fields, Collection $models): Collection
    {
        $audio = $models->first();
        $video = $audio->videos()->first();

        $video->animethemeentries()->each(function (AnimeThemeEntry $firstEntry) use ($audio) {
            $theme = $firstEntry->animetheme()->first();

            $theme->animethemeentries()->each(function (AnimeThemeEntry $entry) use ($audio) {
                $entry->videos()->each(function (Video $video) use ($audio) {
                    Log::info("Associating Audio '{$audio->filename}' with Video '{$video->filename}'");
                    $video->audio()->associate($audio)->save();
                });
            });
        });

        return $models;
    }
}