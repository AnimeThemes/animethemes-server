<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;
use App\Pivots\AnimeThemeEntryVideo;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class AnimeThemeSeeder.
 */
class AnimeThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (WikiPages::YEAR_MAP as $yearPage => $years) {
            // Try not to upset Reddit
            sleep(rand(2, 5));

            // Get JSON of Year page content
            $yearWikiContents = WikiPages::getPageContents($yearPage);
            if ($yearWikiContents === null) {
                continue;
            }

            // We want to proceed line by line
            preg_match_all('/^(.*)$/m', $yearWikiContents, $animeThemeWikiEntries, PREG_SET_ORDER);

            // The current Anime & Group
            $season = null;
            $anime = null;
            $group = null;
            $theme = null;
            $entry = null;

            foreach ($animeThemeWikiEntries as $animeThemeWikiEntry) {
                $wikiEntryLine = html_entity_decode($animeThemeWikiEntry[0]);

                // If Season heading line, set year and season
                // Format: "##{Year} {Season} Season (Quarter)"
                if (preg_match('/^##(\d+).*(Fall|Summer|Spring|Winter).*(?:\\r)?$/', $wikiEntryLine, $animeSeason)) {
                    $season = AnimeSeason::getValue(Str::upper($animeSeason[2]));
                    $anime = null;
                    $group = null;
                    $theme = null;
                    $entry = null;
                    continue;
                }

                // If Anime heading line, attempt to set current Anime
                // Set Season if unset
                // Format: "###[{Anime Name}]({Resource Link})"
                if (preg_match('/^###\[(.*)]\(https:\/\/.*\)(?:\\r)?$/', $wikiEntryLine, $animeName)) {
                    try {
                        // Set current Anime if we have a definitive match
                        // This is not guaranteed as an Anime Name may be inconsistent between indices
                        $matchingAnime = Anime::query()->where('name', html_entity_decode($animeName[1]));
                        $matchingAnime = $matchingAnime->whereIn('year', $years);
                        if (is_int($season)) {
                            $matchingAnime = $matchingAnime->where('season', $season);
                        }
                        if ($matchingAnime->count() === 1) {
                            $anime = $matchingAnime->first();
                            $group = null;
                            $theme = null;
                            $entry = null;
                            continue;
                        }
                    } catch (Exception $e) {
                        Log::error($e->getMessage());
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
                if ($anime instanceof Anime && preg_match('/^\*\*(.*)\*\*(?:\\r)?$/', $wikiEntryLine, $synonymLine)) {
                    $synonyms = html_entity_decode($synonymLine[1]);
                    preg_match_all('/(?|"([^"]+)"|([^,]+))(?:, )?/', $synonyms, $synonymList, PREG_SET_ORDER);
                    foreach ($synonymList as $synonym) {
                        // Create Synonym if it doesn't already exist
                        $text = $synonym[1];
                        if (AnimeSynonym::query()
                            ->where('anime_id', $anime->anime_id)
                            ->where('text', $text)
                            ->doesntExist()
                        ) {
                            Log::info("Creating synonym '{$text}' for anime '{$anime->name}'");
                            $anime->animesynonyms()->create([
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
                if ($anime instanceof Anime && preg_match('/^([a-zA-Z0-9- +]+)(?:\\r)?$/', $wikiEntryLine, $groupName)) {
                    $groupText = Str::of(html_entity_decode($groupName[1]))->trim();
                    if ($groupText->isNotEmpty()) {
                        $group = $groupText->__toString();
                    }
                    $theme = null;
                    $entry = null;
                    continue;
                }

                // If Theme line, attempt to create Theme/Song/Entry
                // Format: "{OP|ED}{Sequence} V{Version} "{Song Title}"|[Webm {Tags}](https://animethemes.moe/video/{Video Basename})|{Episodes}|{Notes}"
                if ($anime instanceof Anime && preg_match('/^(OP|ED)(\d*)(?:\sV(\d*))?.*\"(.*)\".*\|\[Webm.*]\(https:\/\/animethemes\.moe\/video\/(.*)\)\|(.*)\|(.*)(?:\\r)?$/', $wikiEntryLine, $themeMatch)) {
                    $themeType = ThemeType::getValue(Str::upper($themeMatch[1]));
                    $sequence = is_numeric($themeMatch[2]) ? intval($themeMatch[2]) : null;
                    $version = is_numeric($themeMatch[3]) ? intval($themeMatch[3]) : null;
                    $songTitle = html_entity_decode($themeMatch[4]);
                    $videoBasename = $themeMatch[5];
                    $episodes = $themeMatch[6];
                    $notes = Str::of(html_entity_decode($themeMatch[7]))->trim()->__toString();

                    // Create/Update Theme if no version or V1
                    if ($version === null || $version === 1) {
                        // Create Theme if it doesn't exist
                        $theme = AnimeTheme::query()
                            ->where('anime_id', $anime->anime_id)
                            ->where('group', $group)
                            ->where('type', $themeType)
                            ->where(function (Builder $query) use ($sequence) {
                                if (intval($sequence) === 1) {
                                    // Edge Case: "OP|ED" has become "OP1|ED1"
                                    $query->where('sequence', $sequence)->orWhereNull('sequence');
                                } else {
                                    $query->where('sequence', $sequence);
                                }
                            })
                            ->first();

                        if (! $theme instanceof AnimeTheme) {
                            Log::info("Creating theme for anime '{$anime->name}'");
                            $theme = AnimeTheme::factory()
                                ->for($anime)
                                ->createOne([
                                    'group' => $group,
                                    'type' => $themeType,
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
                                ->createOne([
                                    'title' => $songTitle,
                                ]);
                            $song->animethemes()->save($theme);
                        } else {
                            $song->title = $songTitle;

                            // Save song if needed
                            if ($song->isDirty()) {
                                Log::info("Saving song for anime '{$anime->name}'");
                                Log::info(json_encode($song->getDirty()));
                                $song->save();
                            }
                        }

                        // Create Entry and associate to Theme
                        $entry = self::createEntry($version, $episodes, $notes, $theme);

                        // Attach Video to Entry
                        self::attachVideoToEntry($videoBasename, $entry);

                        continue;
                    }

                    // Create Entry of Current Theme if V2+
                    if ($theme !== null && $version > 1) {
                        $entry = self::createEntry($version, $episodes, $notes, $theme);
                        self::attachVideoToEntry($videoBasename, $entry);
                        continue;
                    }
                }

                // If Entry Video line, attach Video to Entry
                // Format: "||[Webm {Tags}](https://animethemes.moe/video/{Video Basename})||"
                if ($entry !== null && preg_match('/^\|\|\[Webm.*]\(https:\/\/animethemes\.moe\/video\/(.*)\)\|\|(?:\\r)?$/', $wikiEntryLine, $videoName)) {
                    $videoBasename = $videoName[1];
                    self::attachVideoToEntry($videoBasename, $entry);
                }
            }
        }
    }

    /**
     * Create Entry and associate to Theme.
     *
     * @param int|null $version
     * @param string $episodes
     * @param string $notes
     * @param AnimeTheme $theme
     * @return AnimeThemeEntry
     */
    protected static function createEntry(
        ?int $version,
        string $episodes,
        string $notes,
        AnimeTheme $theme
    ): AnimeThemeEntry {
        // Create Entry if it doesn't exist
        $entry = AnimeThemeEntry::query()
            ->where('theme_id', $theme->theme_id)
            ->where(function (Builder $query) use ($version) {
                if (intval($version) === 1) {
                    // Edge Case: "OP#|ED#" has become "OP# V1|ED# V1"
                    $query->where('version', $version)->orWhereNull('version');
                } else {
                    $query->where('version', $version);
                }
            })
            ->first();

        if (! $entry instanceof AnimeThemeEntry) {
            Log::info("Creating entry for theme '{$theme->slug}' for anime '{$theme->anime->name}'");
            $entry = AnimeThemeEntry::factory()
                ->for($theme)
                ->createOne([
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
     * Attach video to entry.
     *
     * @param string $videoBasename
     * @param AnimeThemeEntry $entry
     * @return void
     */
    protected static function attachVideoToEntry(string $videoBasename, AnimeThemeEntry $entry)
    {
        $video = Video::query()->where('basename', $videoBasename)->first();
        if ($video instanceof Video && AnimeThemeEntryVideo::query()
                ->where($entry->getKeyName(), $entry->getKey())
                ->where($video->getKeyName(), $video->getKey())
                ->doesntExist()
        ) {
            Log::info("Attaching video '{$video->basename}' to entry '{$entry->getName()}'");
            $entry->videos()->attach($video);
        }
    }
}
