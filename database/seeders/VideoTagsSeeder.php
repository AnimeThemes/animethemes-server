<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class VideoTagsSeeder.
 */
class VideoTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $videoPages = array_keys(WikiPages::YEAR_MAP);
        $videoPages[] = WikiPages::MISC_INDEX;

        foreach ($videoPages as $videoPage) {
            // Try not to upset Reddit
            sleep(rand(2, 5));

            // Get JSON of Year page content
            $yearWikiContents = WikiPages::getPageContents($videoPage);
            if ($yearWikiContents === null) {
                return;
            }

            // Match Tags and basename of Videos
            // Format: "[Webm ({Tag 1, Tag 2, Tag 3...})]({Video Link})
            preg_match_all(
                '/\[Webm.*\((.*)\)]\(https:\/\/animethemes\.moe\/video\/(.*)\)|\[Webm]\(https:\/\/animethemes\.moe\/video\/(.*)\)/m',
                $yearWikiContents,
                $videoWikiEntries,
                PREG_SET_ORDER
            );

            foreach ($videoWikiEntries as $videoWikiEntry) {
                // Video tags are potentially inconsistent, so we make an effort for uniformity
                $videoTags = explode(',', preg_replace('/\s+/', '', Str::upper($videoWikiEntry[1])));
                $videoBasename = count($videoWikiEntry) === 3 ? $videoWikiEntry[2] : $videoWikiEntry[3];

                $video = Video::query()->where(Video::ATTRIBUTE_BASENAME, $videoBasename)->first();
                if (! $video instanceof Video) {
                    continue;
                }

                // Set true/false if tag is included/excluded
                $video->nc = in_array('NC', $videoTags);
                $video->subbed = in_array('SUBBED', $videoTags);
                $video->lyrics = in_array('LYRICS', $videoTags);
                $video->uncen = in_array('UNCEN', $videoTags);

                // Set resolution to first numeric tag or default to 720
                $video->resolution = 720;
                foreach ($videoTags as $videoTag) {
                    if (is_numeric($videoTag)) {
                        $video->resolution = intval($videoTag);
                        break;
                    }
                }

                // Set source type for first matching tag to key
                foreach (VideoSource::getKeys() as $sourceKey) {
                    if (in_array($sourceKey, $videoTags)) {
                        $video->source = VideoSource::getValue($sourceKey);
                        break;
                    }
                }

                // Set overlap type if we have a definitive match or default to 'None'
                $hasTransTag = in_array(VideoOverlap::getKey(VideoOverlap::TRANS), $videoTags);
                $hasOverTag = in_array(VideoOverlap::getKey(VideoOverlap::OVER), $videoTags);
                $video->overlap = VideoOverlap::NONE();
                if ($hasTransTag && ! $hasOverTag) {
                    $video->overlap = VideoOverlap::TRANS();
                }
                if (! $hasTransTag && $hasOverTag) {
                    $video->overlap = VideoOverlap::OVER();
                }

                // Save changes if any to Video
                if ($video->isDirty()) {
                    Log::info("Saving tags for video '$video->basename'");
                    Log::info(json_encode($video->getDirty()));
                    $video->save();
                }
            }
        }
    }
}
