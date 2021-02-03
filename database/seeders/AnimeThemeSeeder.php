<?php

namespace Database\Seeders;

use App\Enums\AnimeSeason;
use App\Enums\ThemeType;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\Song;
use App\Models\Synonym;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnimeThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (WikiPages::YEAR_MAP as $year_page => $years) {

            // Try not to upset Reddit
            sleep(rand(5, 15));

            // Get JSON of Year page content
            $year_wiki_contents = file_get_contents($year_page);
            $year_wiki_json = json_decode($year_wiki_contents);
            $year_wiki_content_md = $year_wiki_json->data->content_md;

            // We want to proceed line by line
            preg_match_all('/^(.*)$/m', $year_wiki_content_md, $anime_theme_wiki_entries, PREG_SET_ORDER);

            // The current Anime & Group
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
                    $season = AnimeSeason::getValue(Str::upper($anime_season[2]));
                    continue;
                    $anime = null;
                    $group = null;
                    $theme = null;
                    $entry = null;
                }

                // If Anime heading line, attempt to set current Anime
                // Set Season if unset
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
                        Log::error($exception);
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
                        // Create Synonym if it doesn't already exist
                        $text = $synonym[1];
                        if (Synonym::where('anime_id', $anime->anime_id)->where('text', $text)->doesntExist()) {
                            Log::info("Creating synonym '{$text}' for anime '{$anime->name}'");
                            $anime->synonyms()->create([
                                'text' => $text,
                            ]);
                        }
                    }
                    $group = null;
                    $theme = null;
                    $entry = null;
                    continue;
                }

                // If group line, attempt to set current group
                // Format: "{Group}"
                if (! is_null($anime) && preg_match('/^([a-zA-Z0-9- \+]+)(?:\\r)?$/', $wiki_entry_line, $group_name)) {
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
                    $theme_type = ThemeType::getValue(Str::upper($theme_match[1]));
                    $sequence = is_numeric($theme_match[2]) ? intval($theme_match[2]) : null;
                    $version = is_numeric($theme_match[3]) ? intval($theme_match[3]) : null;
                    $song_title = html_entity_decode($theme_match[4]);
                    $video_basename = $theme_match[5];
                    $episodes = $theme_match[6];
                    $notes = Str::of(html_entity_decode($theme_match[7]))->trim();

                    // Create/Update Theme if no version or V1
                    if ($version === null || intval($version) === 1) {
                        // Create Theme if it doesn't exist
                        $theme = Theme::where('anime_id', $anime->anime_id)
                            ->where('group', $group)
                            ->where('type', $theme_type)
                            ->where(function ($query) use ($sequence) {
                                if (intval($sequence) === 1) {
                                    // Edge Case: "OP|ED" has become "OP1|ED1"
                                    $query->where('sequence', $sequence)->orWhereNull('sequence');
                                } else {
                                    $query->where('sequence', $sequence);
                                }
                            })
                            ->first();

                        if ($theme === null) {
                            Log::info("Creating theme for anime '{$anime->name}'");
                            $theme = Theme::factory()
                                ->for($anime)
                                ->create([
                                    'group' => $group,
                                    'type' => $theme_type,
                                    'sequence' => $sequence,
                                ]);
                        } else {
                            $theme->sequence = $sequence;

                            // Save theme if needed
                            if ($theme->isDirty()) {
                                Log::info("Saving theme {$theme->slug} for anime '{$anime->name}'");
                                Log::info(json_encode($theme->getDirty()));
                                $theme->save();
                            }
                        }

                        // Create Song if it doesn't exist
                        $song = $theme->song;
                        if ($song === null) {
                            Log::info("Creating song for anime '{$anime->name}'");
                            $song = Song::factory()
                                ->create([
                                    'title' => $song_title,
                                ]);
                            $song->themes()->save($theme);
                        } else {
                            $song->title = $song_title;

                            // Save song if needed
                            if ($song->isDirty()) {
                                Log::info("Saving song for anime '{$anime->name}'");
                                Log::info(json_encode($song->getDirty()));
                                $song->save();
                            }
                        }

                        // Create Entry and associate to Theme
                        $entry = self::create_entry($version, $episodes, $notes, $theme);

                        // Attach Video to Entry
                        self::attach_video_to_entry($video_basename, $entry);

                        continue;
                    }

                    // Create Entry of Current Theme if V2+
                    if (! is_null($theme) && is_numeric($version) && intval($version) > 1) {
                        $entry = self::create_entry($version, $episodes, $notes, $theme);
                        self::attach_video_to_entry($video_basename, $entry);
                        continue;
                    }
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
        // Create Entry if it doesn't exist
        $entry = Entry::where('theme_id', $theme->theme_id)
            ->where(function ($query) use ($version) {
                if (intval($version) === 1) {
                    // Edge Case: "OP#|ED#" has become "OP# V1|ED# V1"
                    $query->where('version', $version)->orWhereNull('version');
                } else {
                    $query->where('version', $version);
                }
            })
            ->first();

        if ($entry === null) {
            Log::info("Creating entry for theme '{$theme->slug}' for anime '{$theme->anime->name}'");
            $entry = Entry::factory()
                ->for($theme)
                ->create([
                    'version' => $version,
                    'episodes' => $episodes,
                    'nsfw' => Str::contains(Str::upper($notes), 'NSFW'),
                    'spoiler' => Str::contains(Str::upper($notes), 'SPOILER'),
                    'notes' => preg_replace('/^(?:NSFW)?(?:,\s)?(?:Spoiler)?$/', '', $notes),
                ]);
        } else {
            $entry->version = $version;
            $entry->episodes = $episodes;
            $entry->nsfw = Str::contains(Str::upper($notes), 'NSFW');
            $entry->spoiler = Str::contains(Str::upper($notes), 'SPOILER');
            $entry->notes = preg_replace('/^(?:NSFW)?(?:,\s)?(?:Spoiler)?$/', '', $notes);

            // Save entry if needed
            if ($entry->isDirty()) {
                Log::info("Saving entry for theme '{$theme->slug}' for anime '{$theme->anime->name}'");
                Log::info(json_encode($entry->getDirty()));
                $entry->save();
            }
        }

        return $entry;
    }

    /**
     * Attach Video to Entry.
     */
    private static function attach_video_to_entry($video_basename, $entry): void
    {
        try {
            $video = Video::where('basename', $video_basename)->firstOrFail();
            if (! $entry->videos->contains($video)) {
                Log::info("Attaching video '{$video->basename}' to entry '{$entry->getName()}'");
                $entry->videos()->attach($video);
            }
        } catch (\Exception $exception) {
            Log::error($exception);
        }
    }
}
