<?php

declare(strict_types=1);

namespace App\Console\Commands;

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
abstract class DatabaseDumpCommand extends Command
{
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

            if (! static::canIncludeTables($connection)) {
                Log::error('DB connection does not support includeTables option');
                $this->error('DB connection does not support includeTables option');

                return 1;
            }
            $dumper->includeTables($this->allowedTables());

            $dumper->setDbName($connection->getDatabaseName())
                ->setUserName(strval($connection->getConfig('username')))
                ->setPassword(strval($connection->getConfig('password')));

            $host = $connection->getConfig('host');
            if ($host !== null) {
                $dumper->setHost(collect($host)->first());
            }

            $port = $connection->getConfig('port');
            if (is_int($port)) {
                $dumper->setPort($port);
            }

            $dumpFile = $this->getDumpFile($create);

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
    public static function canIncludeTables(ConnectionInterface $connection): bool
    {
        // Sqlite version 3.32.0 is required when using the includeTables option
        if ($connection instanceof SQLiteConnection) {
            return version_compare($connection->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION), '3.32.0', '>=');
        }

        return true;
    }

    /**
     * The target path for the database dump.
     * Pattern: "/path/to/project/storage/db-dumps/{path}/animethemes-db-dump-{create?}-{year}-{month}-{day}.sql".
     *
     * @param  bool  $create
     * @return string
     */
    public function getDumpFile(bool $create = false): string
    {
        $filesystem = Storage::disk('db-dumps');

        $filesystem->makeDirectory($this->getDumpFilePath());

        return Str::of($filesystem->path($this->getDumpFilePath()))
            ->append(DIRECTORY_SEPARATOR)
            ->append('animethemes-db-dump-')
            ->append($create ? 'create-' : '')
            ->append(Date::now()->toDateString())
            ->append('.sql')
            ->__toString();
    }

    /**
     * The directory that the file should be dumped to.
     *
     * @return string
     */
    abstract protected function getDumpFilePath(): string;

    /**
     * The list of tables to include in the dump.
     *
     * @return array
     */
    abstract protected function allowedTables(): array;
}
