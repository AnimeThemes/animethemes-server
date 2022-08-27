<?php

declare(strict_types=1);

namespace App\Console\Commands\Wiki;

use App\Console\Commands\DatabaseDumpCommand;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Audio;
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
use App\Pivots\StudioImage;
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
    protected $signature = 'db:dump-wiki
        {--comments : Write additional information in the MySQL dump such as program version, server version and host}
        {--data-only : Dump only the data without the schema in PostgreSQL dump}
        {--default-character-set=utf8 : Specify default character set in MySQL dump}
        {--extended-insert : Use multiple-row insert syntax in MySQL dump}
        {--inserts : Dump data as INSERT commands rather than COPY in PostgreSQL dump}
        {--lock-tables : Lock all tables before dumping them to MySQL dump}
        {--no-create-info : Turn off CREATE TABLE statements in MySQL dump}
        {--quick : Retrieve rows for a table from the server one row at a time in MySQL dump}
        {--set-gtid-purged=AUTO : Add SET GTID_PURGED to output in MySQL dump}
        {--single-transaction : Issue a BEGIN SQL statement before dumping data from server for MySQL dump}
        {--skip-column-statistics : Turn off ANALYZE table statements in the MySQL dump}
        {--skip-comments : Do not write additional information in the MySQL dump}
        {--skip-extended-insert : Turn off extended-insert in MySQL dump}
        {--skip-lock-tables : Turn off locking tables before dumping to MySQL dump}
        {--skip-quick : Do not retrieve rows for a table from the server one row at a time in MySQL dump}
        ';

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
            Audio::TABLE,
            ExternalResource::TABLE,
            Image::TABLE,
            Series::TABLE,
            Song::TABLE,
            Studio::TABLE,
            StudioImage::TABLE,
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
