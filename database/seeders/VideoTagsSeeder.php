<?php

namespace Database\Seeders;

use App\Enums\VideoOverlap;
use App\Enums\VideoSource;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VideoTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $video_pages = collect(WikiPages::YEAR_MAP)->keys()->push(WikiPages::MISC_INDEX);
        foreach ($video_pages as $video_page) {

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Year page content
            $year_wiki_contents = WikiPages::getPageContents($video_page);

            // Match Tags and basename of Videos
            // Format: "[Webm ({Tag 1, Tag 2, Tag 3...})]({Video Link})
            preg_match_all('/\[Webm.*\((.*)\)\]\(https\:\/\/animethemes\.moe\/video\/(.*)\)|\[Webm\]\(https\:\/\/animethemes\.moe\/video\/(.*)\)/m', $year_wiki_contents, $video_wiki_entries, PREG_SET_ORDER);

            foreach ($video_wiki_entries as $video_wiki_entry) {
                // Video tags are potentially inconsistent so we make an effort for uniformity
                $video_tags = explode(',', preg_replace('/\s+/', '', Str::upper($video_wiki_entry[1])));
                $video_basename = count($video_wiki_entry) === 3 ? $video_wiki_entry[2] : $video_wiki_entry[3];

                $video = Video::where('basename', $video_basename)->first();
                if ($video === null) {
                    continue;
                }

                // Set true/false if tag is included/excluded
                $video->nc = in_array('NC', $video_tags);
                $video->subbed = in_array('SUBBED', $video_tags);
                $video->lyrics = in_array('LYRICS', $video_tags);
                $video->uncen = in_array('UNCEN', $video_tags);

                // Set resolution to first numeric tag or default to 720
                $video->resolution = 720;
                foreach ($video_tags as $video_tag) {
                    if (is_numeric($video_tag)) {
                        $video->resolution = intval($video_tag);
                        break;
                    }
                }

                // Set source type for first matching tag to key
                foreach (VideoSource::getKeys() as $source_key) {
                    if (in_array($source_key, $video_tags)) {
                        $video->source = VideoSource::getValue($source_key);
                        break;
                    }
                }

                // Set overlap type if we have a definitive match or default to 'None'
                $has_trans_tag = in_array(VideoOverlap::getKey(VideoOverlap::TRANS), $video_tags);
                $has_over_tag = in_array(VideoOverlap::getKey(VideoOverlap::OVER), $video_tags);
                $video->overlap = VideoOverlap::NONE;
                if ($has_trans_tag && ! $has_over_tag) {
                    $video->overlap = VideoOverlap::TRANS;
                }
                if (! $has_trans_tag && $has_over_tag) {
                    $video->overlap = VideoOverlap::OVER;
                }

                // Save changes if any to Video
                if ($video->isDirty()) {
                    Log::info("Saving tags for video '{$video->basename}'");
                    Log::info(json_encode($video->getDirty()));
                    $video->save();
                }
            }
        }
    }
}
