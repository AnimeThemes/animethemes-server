<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Databases\PostgreSql;
use Spatie\DbDumper\Databases\Sqlite;

class DatabaseDumpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Produces sanitized database dump, targeting wiki-related tables for local seeding purposes';

    /**
     * The list of tables to include in the dump.
     *
     * @var array
     */
    protected $allowedTables = [
        'anime',
        'anime_image',
        'anime_resource',
        'anime_series',
        'artist',
        'artist_image',
        'artist_member',
        'artist_resource',
        'artist_song',
        'entry',
        'entry_video',
        'image',
        'resource',
        'series',
        'song',
        'synonym',
        'theme',
        'video',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $dumpFile = $this->getDumpFile();

            // Specify the PDO connection type so that the correct utility is run
            $dbConnection = DB::connection();
            $connectionName = $dbConnection->getName();
            switch ($connectionName) {
                case 'sqlite':
                    Sqlite::create()
                        ->setDbName($dbConnection->getDatabaseName())
                        ->setUserName(strval($dbConnection->getConfig('username')))
                        ->setPassword(strval($dbConnection->getConfig('password')))
                        ->includeTables($this->allowedTables)
                        ->dumpToFile($dumpFile);
                    break;
                case 'mysql':
                    MySql::create()
                        ->doNotCreateTables()
                        ->setDbName($dbConnection->getDatabaseName())
                        ->setUserName(strval($dbConnection->getConfig('username')))
                        ->setPassword(strval($dbConnection->getConfig('password')))
                        ->setHost($dbConnection->getConfig('host'))
                        ->setPort($dbConnection->getConfig('port'))
                        ->includeTables($this->allowedTables)
                        ->dumpToFile($dumpFile);
                    break;
                case 'pgsql':
                    PostgreSql::create()
                        ->doNotCreateTables()
                        ->setDbName($dbConnection->getDatabaseName())
                        ->setUserName(strval($dbConnection->getConfig('username')))
                        ->setPassword(strval($dbConnection->getConfig('password')))
                        ->setHost($dbConnection->getConfig('host'))
                        ->setPort($dbConnection->getConfig('port'))
                        ->includeTables($this->allowedTables)
                        ->dumpToFile($dumpFile);
                    break;
                default:
                    Log::error("Unrecognized connection '{$connectionName}'");
                    $this->error("Unrecognized connection '{$connectionName}'");
                    break;
            }

            // Assume success if no exceptions were thrown
            // The library will check if the file exists and is not empty
            Log::info("Database dump '{$dumpFile}' has been created");
            $this->info("Database dump '{$dumpFile}' has been created");
        } catch (Exception $exception) {
            Log::error($exception);
            $this->error($exception->getMessage());

            return 1;
        }

        return 0;
    }

    /**
     * The target path for the database dump.
     * Pattern: "/path/to/project/storage/db-dumps/animethemes-db-dump-{year}-{month}-{day}.sql".
     *
     * @return string
     */
    private function getDumpFile()
    {
        $dumpFile = Str::of('db-dumps')
            ->append(DIRECTORY_SEPARATOR)
            ->append('animethemes-db-dump-')
            ->append(Carbon::now()->toDateString())
            ->append('.sql')
            ->__toString();

        return storage_path($dumpFile);
    }
}
