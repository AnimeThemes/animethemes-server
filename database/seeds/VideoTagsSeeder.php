<?php

use App\Enums\OverlapType;
use App\Enums\SourceType;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class VideoTagsSeeder extends Seeder
{

    // Hard-coded addresses of year pages
    // I don't really care about making this more elegant
    const YEAR_PAGES = [
        'https://www.reddit.com/r/AnimeThemes/wiki/60s.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/70s.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/80s.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/90s.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2000.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2001.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2002.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2003.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2004.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2005.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2006.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2007.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2008.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2009.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2010.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2011.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2012.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2013.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2014.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2015.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2016.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2017.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2018.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2019.json',
        'https://www.reddit.com/r/AnimeThemes/wiki/2020.json',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (VideoTagsSeeder::YEAR_PAGES as $year_page) {

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Year page content
            $year_wiki_contents = file_get_contents($year_page);
            $year_wiki_json = json_decode($year_wiki_contents);
            $year_wiki_content_md = $year_wiki_json->data->content_md;

            // Match Tags and basename of Videos
            // Format: "[Webm ({Tag 1, Tag 2, Tag 3...})]({Video Link})
            preg_match_all('/\[Webm.*\((.*)\)\]\(https\:\/\/animethemes\.moe\/video\/(.*)\)|\[Webm\]\(https\:\/\/animethemes\.moe\/video\/(.*)\)/m', $year_wiki_content_md, $video_wiki_entries, PREG_SET_ORDER);

            foreach ($video_wiki_entries as $video_wiki_entry) {
                // Video tags are potentially inconsistent so we make an effort for uniformity
                $video_tags = explode(',', preg_replace('/\s+/', '', strtoupper($video_wiki_entry[1])));
                $video_basename = count($video_wiki_entry) === 3 ? $video_wiki_entry[2] : $video_wiki_entry[3];

                try {
                    $video = Video::where('basename', $video_basename)->firstOrFail();

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
                    foreach (SourceType::getKeys() as $source_key) {
                        if (in_array($source_key, $video_tags)) {
                            $video->source = SourceType::getValue($source_key);
                            break;
                        }
                    }

                    // Set overlap type if we have a definitive match or default to 'None'
                    $has_trans_tag = in_array(OverlapType::getKey(OverlapType::TRANS), $video_tags);
                    $has_over_tag = in_array(OverlapType::getKey(OverlapType::OVER), $video_tags);
                    $video->overlap = OverlapType::NONE;
                    if ($has_trans_tag && !$has_over_tag) {
                        $video->overlap = OverlapType::TRANS;
                    }
                    if (!$has_trans_tag && $has_over_tag) {
                        $video->overlap = OverlapType::OVER;
                    }

                    // Save changes if any to Video
                    if ($video->isDirty()) {
                        $video->save();
                    }
                } catch (Exception $exception) {
                    LOG::error($exception);
                }
            }
        }
    }
}
