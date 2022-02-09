<?php

declare(strict_types=1);

namespace App\Console\Commands\Wiki;

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
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PDO;
use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Databases\PostgreSql;
use Spatie\DbDumper\Databases\Sqlite;
use Spatie\DbDumper\DbDumper;

/**
 * Class DatabaseDumpCommand.
 */
class DatabaseDumpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:dump {--C|create : Whether the dumper should include create table statements}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Produces sanitized database dump, targeting wiki-related tables for seeding purposes';

    /**
     * The list of tables to include in the dump.
     *
     * @var string[]
     */
    protected array $allowedTables = [
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
        Image::TABLE,
        ExternalResource::TABLE,
        Series::TABLE,
        Song::TABLE,
        Studio::TABLE,
        StudioResource::TABLE,
        Video::TABLE,
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $create = $this->option('create');

        try {
            $connection = DB::connection();

            $dumper = $this->getDumper($connection, $create);
            if ($dumper === null) {
                Log::error("Unrecognized connection '{$connection->getName()}'");
                $this->error("Unrecognized connection '{$connection->getName()}'");

                return 1;
            }

            $dumper->setDbName($connection->getDatabaseName())
                ->setUserName(strval($connection->getConfig('username')))
                ->setPassword(strval($connection->getConfig('password')));

            if ($this->canIncludeTables($connection)) {
                $dumper->includeTables($this->allowedTables);
            }

            $host = $connection->getConfig('host');
            if ($host !== null) {
                $dumper->setHost(collect($host)->first());
            }

            $port = $connection->getConfig('port');
            if (is_int($port)) {
                $dumper->setPort($port);
            }

            $dumpFile = static::getDumpFile($create);

            $dumper->dumpToFile($dumpFile);

            // Assume success if no exceptions were thrown
            // The library will check if the file exists and is not empty
            Log::info("Database dump '$dumpFile' has been created");
            $this->info("Database dump '$dumpFile' has been created");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->error($e->getMessage());

            return 1;
        }

        return 0;
    }

    /**
     * The target path for the database dump.
     * Pattern: "/path/to/project/storage/db-dumps/animethemes-db-dump-{create?}-{year}-{month}-{day}.sql".
     *
     * @param  bool  $create
     * @return string
     */
    public static function getDumpFile(bool $create = false): string
    {
        $filesystem = Storage::disk('db-dumps');

        return Str::of($filesystem->path(''))
            ->append('animethemes-db-dump-')
            ->append($create ? 'create-' : '')
            ->append(Date::now()->toDateString())
            ->append('.sql')
            ->__toString();
    }

    /**
     * Get the dumper for the database connection.
     *
     * @param  ConnectionInterface  $connection
     * @param  bool  $create
     * @return DbDumper|null
     */
    protected function getDumper(ConnectionInterface $connection, bool $create): ?DbDumper
    {
        return match (get_class($connection)) {
            SQLiteConnection::class => Sqlite::create(),
            MySqlConnection::class => $create ? MySql::create() : MySql::create()->doNotCreateTables(),
            PostgresConnection::class => $create ? PostgreSql::create() : PostgreSql::create()->doNotCreateTables(),
            default => null,
        };
    }

    /**
     * Determine if the database connection supports table inclusion.
     *
     * @param  ConnectionInterface  $connection
     * @return bool
     */
    protected function canIncludeTables(ConnectionInterface $connection): bool
    {
        // Sqlite version 3.32.0 is required when using the includeTables option
        if ($connection instanceof SQLiteConnection) {
            Log::warning('SQLite version does not support includeTables option');

            return version_compare($connection->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION), '3.32.0', '>=');
        }

        return true;
    }
}
