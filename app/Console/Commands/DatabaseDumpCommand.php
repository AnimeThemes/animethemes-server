<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PDO;
use RuntimeException;
use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Databases\PostgreSql;
use Spatie\DbDumper\Databases\Sqlite;
use Spatie\DbDumper\DbDumper;
use Spatie\DbDumper\Exceptions\CannotSetParameter;

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
        $validator = Validator::make($this->options(), [
            'default-character-set' => ['string'],
            'set-gtid-purged' => [Rule::in(['OFF', 'ON', 'AUTO'])->__toString()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return 1;
        }

        try {
            /** @var Connection $connection */
            $connection = DB::connection();

            $dumper = $this->getDumper($connection);
            if ($dumper === null) {
                Log::error("Unrecognized connection '{$connection->getName()}'");
                $this->error("Unrecognized connection '{$connection->getName()}'");

                return 1;
            }

            $dumpFile = $this->getDumpFile();

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
     * @param  Connection  $connection
     * @return DbDumper|null
     *
     * @throws CannotSetParameter
     */
    protected function getDumper(Connection $connection): ?DbDumper
    {
        return match (get_class($connection)) {
            SQLiteConnection::class => $this->prepareSqliteDumper($connection),
            MySqlConnection::class => $this->prepareMySqlDumper($connection),
            PostgresConnection::class => $this->preparePostgreSqlDumper($connection),
            default => null,
        };
    }

    /**
     * Configure Sqlite database dumper.
     *
     * @param  Connection  $connection
     * @return Sqlite
     *
     * @throws RuntimeException
     * @throws CannotSetParameter
     */
    protected function prepareSqliteDumper(Connection $connection): Sqlite
    {
        if (version_compare($connection->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION), '3.32.0', '<')) {
            throw new RuntimeException('DB connection does not support includeTables option');
        }

        $dumper = Sqlite::create();

        $dumper->setDbName($connection->getDatabaseName());
        $dumper->includeTables($this->allowedTables());

        return $dumper;
    }

    /**
     * Configure MySQL database dumper.
     *
     * @param  Connection  $connection
     * @return MySql
     *
     * @throws CannotSetParameter
     */
    protected function prepareMySqlDumper(Connection $connection): MySql
    {
        $dumper = MySql::create();

        $dumper->setDbName($connection->getDatabaseName());
        $dumper->setUserName(strval($connection->getConfig('username')));
        $dumper->setPassword(strval($connection->getConfig('password')));

        // Managed database requires the host to be set as an input option
        $hostConfig = $connection->getConfig('host');
        if ($hostConfig !== null) {
            $host = collect($hostConfig)->first();
            $dumper->addExtraOption("-h $host");
            $dumper->setHost($host);
        }

        // Managed database requires the port to be set as an input option
        $port = $connection->getConfig('port');
        if (is_int($port)) {
            $dumper->addExtraOption("-P $port");
            $dumper->setPort($port);
        }

        $dumper->includeTables($this->allowedTables());

        if ($this->option('comments')) {
            $dumper->dontSkipComments();
        }

        if ($this->option('skip-comments')) {
            $dumper->skipComments();
        }

        if ($this->option('extended-insert')) {
            $dumper->useExtendedInserts();
        }

        if ($this->option('skip-extended-insert')) {
            $dumper->dontUseExtendedInserts();
        }

        $this->option('single-transaction')
            ? $dumper->useSingleTransaction()
            : $dumper->dontUseSingleTransaction();

        if ($this->option('lock-tables')) {
            $dumper->dontSkipLockTables();
        }

        if ($this->option('skip-lock-tables')) {
            $dumper->skipLockTables();
        }

        if ($this->option('skip-column-statistics')) {
            $dumper->doNotUseColumnStatistics();
        }

        if ($this->option('quick')) {
            $dumper->useQuick();
        }

        if ($this->option('skip-quick')) {
            $dumper->dontUseQuick();
        }

        if ($this->hasOption('default-character-set')) {
            $dumper->setDefaultCharacterSet($this->option('default-character-set'));
        }

        if ($this->hasOption('set-gtid-purged')) {
            $dumper->setGtidPurged($this->option('set-gtid-purged'));
        }

        if ($this->option('no-create-info')) {
            $dumper->doNotCreateTables();
        }

        return $dumper;
    }

    /**
     * Configure PostgreSql database dumper.
     *
     * @param  Connection  $connection
     * @return PostgreSql
     *
     * @throws CannotSetParameter
     */
    protected function preparePostgreSqlDumper(Connection $connection): PostgreSql
    {
        $dumper = PostgreSql::create();

        $dumper->setDbName($connection->getDatabaseName());
        $dumper->setUserName(strval($connection->getConfig('username')));
        $dumper->setPassword(strval($connection->getConfig('password')));

        $host = $connection->getConfig('host');
        if ($host !== null) {
            $dumper->setHost(collect($host)->first());
        }

        $port = $connection->getConfig('port');
        if (is_int($port)) {
            $dumper->setPort($port);
        }

        $dumper->includeTables($this->allowedTables());

        if ($this->option('inserts')) {
            $dumper->useInserts();
        }

        if ($this->option('data-only')) {
            $dumper->doNotCreateTables();
        }

        return $dumper;
    }

    /**
     * The target path for the database dump.
     * Pattern: "/path/to/project/storage/db-dumps/{path}/animethemes-db-dump-{year}-{month}-{day}.sql".
     *
     * @return string
     */
    public function getDumpFile(): string
    {
        $filesystem = Storage::disk('db-dumps');

        $filesystem->makeDirectory($this->getDumpFilePath());

        return Str::of($filesystem->path($this->getDumpFilePath()))
            ->append(DIRECTORY_SEPARATOR)
            ->append('animethemes-db-dump-')
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
