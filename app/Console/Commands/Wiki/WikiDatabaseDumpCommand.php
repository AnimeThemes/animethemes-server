<?php

declare(strict_types=1);

namespace App\Console\Commands\Wiki;

use App\Console\Commands\DatabaseDumpCommand;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Video;
use App\Pivots\AnimeImage;
use App\Pivots\AnimeResource;
use App\Pivots\AnimeSeries;
use App\Pivots\AnimeStudio;
use App\Pivots\AnimeThemeEntryVideo;
use App\Pivots\ArtistImage;
use App\Pivots\ArtistMember;
use App\Pivots\ArtistResource;
use App\Pivots\ArtistSong;
use App\Pivots\StudioResource;

/**
 * Class WikiDatabaseDumpCommand.
 */
class WikiDatabaseDumpCommand extends DatabaseDumpCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:dump-wiki {--C|create : Whether the dumper should include create table statements}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Produces sanitized database dump, targeting wiki-related tables for seeding purposes';

    /**
     * The list of tables to include in the dump.
     *
     * @return array
     */
    protected function allowedTables(): array
    {
        return [
            Anime::TABLE,
            AnimeImage::TABLE,
            AnimeResource::TABLE,
            AnimeSeries::TABLE,
            AnimeStudio::TABLE,
            AnimeSynonym::TABLE,
            AnimeTheme::TABLE,
            AnimeThemeEntry::TABLE,
            AnimeThemeEntryVideo::TABLE,
            Artist::TABLE,
            ArtistImage::TABLE,
            ArtistMember::TABLE,
            ArtistResource::TABLE,
            ArtistSong::TABLE,
            ExternalResource::TABLE,
            Image::TABLE,
            Series::TABLE,
            Song::TABLE,
            Studio::TABLE,
            StudioResource::TABLE,
            Video::TABLE,
        ];
    }

    /**
     * The directory that the file should be dumped to.
     *
     * @return string
     */
    protected function getDumpFilePath(): string
    {
        return 'wiki';
    }
}
