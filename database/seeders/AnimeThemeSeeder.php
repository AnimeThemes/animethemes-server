<?php

namespace Database\Seeders;

use App\Enums\AnimeSeason;
use App\Enums\ThemeType;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\Song;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnimeThemeSeeder extends Seeder
{
    // Hard-coded addresses of year pages
    // I don't really care about making this more elegant
    const YEAR_PAGES = [
        'https://www.reddit.com/r/AnimeThemes/wiki/60s.json' => [1960, 1961, 1962, 1963, 1964, 1965, 1966, 1967, 1968, 1969],
        'https://www.reddit.com/r/AnimeThemes/wiki/70s.json' => [1970, 1971, 1972, 1973, 1974, 1975, 1976, 1977, 1978, 1979],
        'https://www.reddit.com/r/AnimeThemes/wiki/80s.json' => [1980, 1981, 1982, 1983, 1984, 1985, 1986, 1987, 1988, 1989],
        'https://www.reddit.com/r/AnimeThemes/wiki/90s.json' => [1990, 1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999],
        'https://www.reddit.com/r/AnimeThemes/wiki/2000.json' => [2000],
        'https://www.reddit.com/r/AnimeThemes/wiki/2001.json' => [2001],
        'https://www.reddit.com/r/AnimeThemes/wiki/2002.json' => [2002],
        'https://www.reddit.com/r/AnimeThemes/wiki/2003.json' => [2003],
        'https://www.reddit.com/r/AnimeThemes/wiki/2004.json' => [2004],
        'https://www.reddit.com/r/AnimeThemes/wiki/2005.json' => [2005],
        'https://www.reddit.com/r/AnimeThemes/wiki/2006.json' => [2006],
        'https://www.reddit.com/r/AnimeThemes/wiki/2007.json' => [2007],
        'https://www.reddit.com/r/AnimeThemes/wiki/2008.json' => [2008],
        'https://www.reddit.com/r/AnimeThemes/wiki/2009.json' => [2009],
        'https://www.reddit.com/r/AnimeThemes/wiki/2010.json' => [2010],
        'https://www.reddit.com/r/AnimeThemes/wiki/2011.json' => [2011],
        'https://www.reddit.com/r/AnimeThemes/wiki/2012.json' => [2012],
        'https://www.reddit.com/r/AnimeThemes/wiki/2013.json' => [2013],
        'https://www.reddit.com/r/AnimeThemes/wiki/2014.json' => [2014],
        'https://www.reddit.com/r/AnimeThemes/wiki/2015.json' => [2015],
        'https://www.reddit.com/r/AnimeThemes/wiki/2016.json' => [2016],
        'https://www.reddit.com/r/AnimeThemes/wiki/2017.json' => [2017],
        'https://www.reddit.com/r/AnimeThemes/wiki/2018.json' => [2018],
        'https://www.reddit.com/r/AnimeThemes/wiki/2019.json' => [2019],
        'https://www.reddit.com/r/AnimeThemes/wiki/2020.json' => [2020],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (AnimeThemeSeeder::YEAR_PAGES as $year_page => $years) {

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Year page content
            $year_wiki_contents = file_get_contents($year_page);
            $year_wiki_json = json_decode($year_wiki_contents);
            $year_wiki_content_md = $year_wiki_json->data->content_md;

            // We want to proceed line by line
            preg_match_all('/^(.*)$/m', $year_wiki_content_md, $anime_theme_wiki_entries, PREG_SET_ORDER);

            // The current Anime & Group
            $year = null;
            $season = null;
            $anime = null;
            $group = null;
            $theme = null;
            $entry = null;

            foreach ($anime_theme_wiki_entries as $anime_theme_wiki_entry) {
                $wiki_entry_line = html_entity_decode($anime_theme_wiki_entry[0]);

                // If Season heading line, set year and season
                // Format: "##{Year} {Season} Season (Quarter)"
                if (preg_match('/^##(\d+).*(Fall|Summer|Spring|Winter).*(?:\\r)?$/', $wiki_entry_line, $anime_season)) {
                    $year = intval($anime_season[1]);
                    $season = AnimeSeason::getValue(Str::upper($anime_season[2]));
                    continue;
                }

                // If Anime heading line, attempt to set current Anime
                // Set Year and Season if unset
                // Format: "###[{Anime Name}]({Resource Link})"
                if (preg_match('/^###\[(.*)\]\(https\:\/\/.*\)(?:\\r)?$/', $wiki_entry_line, $anime_name)) {
                    try {
                        // Set current Anime if we have a definitive match
                        // This is not guaranteed as an Anime Name may be inconsistent between indices
                        $matching_anime = Anime::where('name', html_entity_decode($anime_name[1]));
                        $matching_anime = $matching_anime->whereIn('year', $years);
                        if (is_int($season)) {
                            $matching_anime = $matching_anime->where('season', $season);
                        }
                        if ($matching_anime->count() === 1) {
                            $anime = $matching_anime->first();
                            $group = null;
                            $theme = null;
                            $entry = null;
                            continue;
                        }
                    } catch (\Exception $exception) {
                        LOG::error($exception);
                    }

                    $anime = null;
                    $group = null;
                    $theme = null;
                    $entry = null;
                    continue;
                }

                // If Synonym heading line, attempt to set Synonyms for Anime
                // Format: "{**Synonym 1**, **Synonym 2**, **Synonym 3**, ...}"
                // Note: Line may use '"' as qualifier
                if (! is_null($anime) && preg_match('/^\*\*(.*)\*\*(?:\\r)?$/', $wiki_entry_line, $synonym_line)) {
                    $synonyms = html_entity_decode($synonym_line[1]);
                    preg_match_all('/(?|"([^"]+)"|([^,]+))(?:, )?/', $synonyms, $synonym_list, PREG_SET_ORDER);
                    foreach ($synonym_list as $synonym) {
                        $anime->synonyms()->create([
                            'text' => $synonym[1],
                        ]);
                    }
                    continue;
                }

                // If group line, attempt to set current group
                // Format: "{Group}"
                if (! is_null($anime) && preg_match('/^([a-zA-Z0-9- ]+)(?:\\r)?$/', $wiki_entry_line, $group_name)) {
                    $group_text = Str::of(html_entity_decode($group_name[1]))->trim();
                    if (! empty($group_text)) {
                        $group = $group_text;
                    }
                    $theme = null;
                    $entry = null;
                    continue;
                }

                // If Theme line, attempt to create Theme/Song/Entry
                // Format: "{OP|ED}{Sequence} V{Version} "{Song Title}"|[Webm {Tags}](https://animethemes.moe/video/{Video Basename})|{Episodes}|{Notes}"
                if (! is_null($anime) && preg_match('/^(OP|ED)(\d*)(?:\sV(\d*))?.*\"(.*)\".*\|\[Webm.*\]\(https\:\/\/animethemes\.moe\/video\/(.*)\)\|(.*)\|(.*)(?:\\r)?$/', $wiki_entry_line, $theme_match)) {
                    $theme_type = $theme_match[1];
                    $sequence = $theme_match[2];
                    $version = $theme_match[3];
                    $song_title = html_entity_decode($theme_match[4]);
                    $video_basename = $theme_match[5];
                    $episodes = $theme_match[6];
                    $notes = Str::of(html_entity_decode($theme_match[7]))->trim();

                    // Create Theme if no version or V1
                    if (! is_numeric($version) || intval($version) === 1) {
                        // Create Song
                        $song = Song::create([
                            'title' => $song_title,
                        ]);

                        // Create Theme
                        $theme = new Theme;
                        $theme->group = $group;
                        $theme->type = ThemeType::getValue(Str::upper($theme_type));
                        if (is_numeric($sequence)) {
                            $theme->sequence = intval($sequence);
                        }

                        // Associate Song & Anime to Theme
                        $theme->anime()->associate($anime);
                        $theme->song()->associate($song);
                        $theme->save();

                        // Create Entry and associate to Theme
                        $entry = self::create_entry($version, $episodes, $notes, $theme);

                        // Attach Video to Entry
                        self::attach_video_to_entry($video_basename, $entry);
                    }

                    // Create Entry of Current Theme if V2+
                    if (! is_null($theme) && is_numeric($version) && intval($version) > 1) {
                        $entry = self::create_entry($version, $episodes, $notes, $theme);
                        self::attach_video_to_entry($video_basename, $entry);
                    }

                    continue;
                }

                // If Entry Video line, attach Video to Entry
                // Format: "||[Webm {Tags}](https://animethemes.moe/video/{Video Basename})||"
                if (! is_null($entry) && preg_match('/^\|\|\[Webm.*\]\(https\:\/\/animethemes\.moe\/video\/(.*)\)\|\|(?:\\r)?$/', $wiki_entry_line, $video_name)) {
                    $video_basename = $video_name[1];
                    self::attach_video_to_entry($video_basename, $entry);
                }
            }
        }
    }

    /**
     * Create Entry and associate to Theme.
     *
     * @return \App\Models\Entry  $entry
     */
    private static function create_entry($version, $episodes, $notes, $theme)
    {
        $entry = new Entry;

        if (is_numeric($version)) {
            $entry->version = intval($version);
        }
        $entry->episodes = $episodes;
        if (Str::contains(Str::upper($notes), 'NSFW')) {
            $entry->nsfw = true;
        }
        if (Str::contains(Str::upper($notes), 'SPOILER')) {
            $entry->spoiler = true;
        }
        $entry->notes = preg_replace('/^(?:NSFW)?(?:,\s)?(?:Spoiler)?$/', '', $notes);

        $entry->theme()->associate($theme);
        $entry->save();

        return $entry;
    }

    /**
     * Attach Video to Entry.
     */
    private static function attach_video_to_entry($video_basename, $entry): void
    {
        try {
            $video = Video::where('basename', $video_basename)->firstOrFail();
            $entry->videos()->attach($video);
        } catch (\Exception $exception) {
            LOG::error($exception);
        }
    }
}
