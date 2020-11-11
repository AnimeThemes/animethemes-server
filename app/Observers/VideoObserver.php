<?php

namespace App\Observers;

use App\Enums\VideoSource;
use App\Models\Video;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VideoObserver
{
    /**
     * Handle the video "creating" event.
     *
     * @param  \App\Models\Video  $video
     * @return void
     */
    public function creating(Video $video)
    {
        try {
            // Match Tags of filename
            // Format: "{Base Name}-{OP|ED}{Sequence}v{Version}-{Tags}"
            preg_match('/^.*\-(?:OP|ED).*\-(.*)$/', $video->filename, $tags_match);

            // Check if the filename has tags, which is not guaranteed
            if (! empty($tags_match)) {
                $tags = $tags_match[1];

                // Set true/false if tag is included/excluded
                $video->nc = Str::contains($tags, 'NC');
                $video->subbed = Str::contains($tags, 'Subbed');
                $video->lyrics = Str::contains($tags, 'Lyrics');
                // Note: Our naming convention does not include "Uncen"

                // Set resolution to numeric tag if included
                preg_match('/\d+/', $tags, $resolution);
                if (! empty($resolution)) {
                    $video->resolution = intval($resolution[0]);
                }

                // Special cases for implicit resolution
                if (in_array($tags, ['NCBD', 'NCBDLyrics'])) {
                    $video->resolution = 720;
                }

                // Set source type for first matching tag to key
                foreach (VideoSource::getKeys() as $source_key) {
                    if (Str::contains($tags, $source_key)) {
                        $video->source = VideoSource::getValue($source_key);
                        break;
                    }
                }

                // Note: Our naming convention does not include Overlap type
            }
        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }
}
