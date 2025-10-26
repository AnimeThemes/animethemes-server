<?php

declare(strict_types=1);

namespace App\Actions\Storage\Admin\Dump;

use App\Actions\ActionResult;
use App\Constants\Config\DumpConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Admin\Dump;
use Exception;
use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PDO;
use RuntimeException;
use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Databases\PostgreSql;
use Spatie\DbDumper\Databases\Sqlite;
use Spatie\DbDumper\DbDumper;
use Spatie\DbDumper\Exceptions\CannotSetParameter;

abstract class DumpAction
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function __construct(protected readonly array $options = []) {}

    /**
     * @throws Exception
     */
    public function handle(): ActionResult
    {
        $dumpFile = $this->getDumpFile();

        try {
            $connection = DB::connection();

            $dumper = $this->getDumper($connection);
            if (! $dumper instanceof DbDumper) {
                return new ActionResult(
                    ActionStatus::FAILED,
                    "Unrecognized connection '{$connection->getName()}'"
                );
            }

            // First, Dump file to temporary location
            // Writing to disks is not fully supported
            $dumper->dumpToFile($dumpFile);

            // Then, store dump file in intended location
            /** @var FilesystemAdapter $fs */
            $fs = Storage::disk(Config::get(DumpConstants::DISK_QUALIFIED));
            $path = $fs->putFileAs('', $dumpFile, File::basename($dumpFile));

            // Finally, delete the temporary file
            File::delete($dumpFile);
        } catch (Exception $e) {
            return new ActionResult(
                ActionStatus::FAILED,
                $e->getMessage()
            );
        }

        Dump::query()->create([
            Dump::ATTRIBUTE_PATH => $path,
        ]);

        return new ActionResult(
            ActionStatus::PASSED,
            "Database dump '$dumpFile' has been created",
        );
    }

    /**
     * Get the dumper for the database connection.
     *
     * @throws CannotSetParameter
     */
    protected function getDumper(Connection $connection): ?DbDumper
    {
        if ($connection instanceof SQLiteConnection) {
            return $this->prepareSqliteDumper($connection);
        }

        if ($connection instanceof MySqlConnection) {
            return $this->prepareMySqlDumper($connection);
        }

        if ($connection instanceof PostgresConnection) {
            return $this->preparePostgreSqlDumper($connection);
        }

        return null;
    }

    /**
     * Configure Sqlite database dumper.
     *
     * @throws RuntimeException
     * @throws CannotSetParameter
     */
    protected function prepareSqliteDumper(Connection $connection): Sqlite
    {
        throw_if(version_compare($connection->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION), '3.32.0', '<'), RuntimeException::class, 'DB connection does not support includeTables option');

        $dumper = Sqlite::create();

        $dumper->setDbName($connection->getDatabaseName());
        $dumper->includeTables($this->allowedTables());

        return $dumper;
    }

    /**
     * Configure MySQL database dumper.
     *
     * @throws CannotSetParameter
     */
    protected function prepareMySqlDumper(Connection $connection): MySql
    {
        $dumper = MySql::create();

        $dumper->setDbName($connection->getDatabaseName());
        $dumper->setUserName(strval($connection->getConfig('username')));
        $dumper->setPassword(strval($connection->getConfig('password')));

        $hostConfig = $connection->getConfig('host');
        $socketConfig = $connection->getConfig('unix_socket');
        if ($socketConfig !== null) {
            $dumper->setSocket(strval($socketConfig));
        } elseif ($hostConfig !== null) {
            $dumper->setHost(collect($hostConfig)->first());
        }

        $port = $connection->getConfig('port');
        if (is_numeric($port)) {
            $dumper->setPort(intval($port));
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
     * Determine if the string option is set.
     */
    protected function hasOption(string $key): bool
    {
        return is_string(Arr::get($this->options, $key));
    }

    /**
     * Get the option by key.
     */
    protected function option(string $key): mixed
    {
        return Arr::get($this->options, $key);
    }

    /**
     * The temporary path for the database dump.
     * Note: The dumper library does not support writing to disk, so we have to write to the local filesystem first.
     * Pattern: "animethemes-db-dump-{concern}-{milliseconds from epoch}.sql".
     */
    abstract protected function getDumpFile(): string;

    /**
     * The list of tables to include in the dump.
     */
    abstract protected function allowedTables(): array;
}
