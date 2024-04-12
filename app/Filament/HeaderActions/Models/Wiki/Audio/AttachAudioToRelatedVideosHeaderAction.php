<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Audio;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Filament\Actions\Action;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Class AttachAudioToRelatedVideosHeaderAction.
 */
class AttachAudioToRelatedVideosHeaderAction extends Action implements ShouldQueue
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn (Model $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given models.
     *
     * @param  Model  $audio
     * @param  array  $data
     * @return void
     */
    public function handle(Model $audio, array $data): void
    {
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
    }
}
