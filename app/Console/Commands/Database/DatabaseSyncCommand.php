<?php

declare(strict_types=1);

namespace App\Console\Commands\Database;

use App\Actions\Storage\Admin\Dump\DumpContentAction;
use App\Console\Commands\BaseCommand;
use Database\Seeders\Admin\Feature\FeatureSeeder;
use Database\Seeders\Auth\Permission\PermissionSeeder;
use Database\Seeders\Auth\Role\RoleSeeder;
use Database\Seeders\FactoryDataSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

#[Signature(
    'db:sync
    {--drop : Determine whether the existing database should be re-created}
    {--skip-factory : Skip the fake data creation}'
)]
#[Description('Sync the local database with the latest dumps')]
class DatabaseSyncCommand extends BaseCommand
{
    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('Command not allowed in production.');

            return 1;
        }

        $database = Schema::getConnection()->getDatabaseName();

        if ($this->option('drop')) {
            $this->info("Dropping database {$database}");
            Schema::dropDatabaseIfExists($database);

            $this->info("Creating database {$database}");
            Schema::createDatabase($database);
        }

        if (! $this->option('drop')) {
            Schema::withoutForeignKeyConstraints(function (): void {
                foreach ([
                    ...DumpContentAction::allowedTables(),
                ] as $table) {
                    $this->info("Truncating table {$table}");
                    DB::table($table)->truncate();
                }
            });
        }

        DB::statement("USE `{$database}`");

        $this->info('Migrating database');
        $this->call('migrate');

        if (! $this->option('skip-factory')) {
            $this->info('Adding factory data');
            $this->call('db:seed', ['class' => FactoryDataSeeder::class]);
        }

        $this->info('Seeding permissions');
        $this->call('db:seed', ['class' => PermissionSeeder::class]);

        $this->info('Seeding roles');
        $this->call('db:seed', ['class' => RoleSeeder::class]);

        $this->info('Seeding features');
        $this->call('db:seed', ['class' => FeatureSeeder::class]);

        $this->info('Importing models for scout');
        $this->call('scout:import-all');

        return 0;
    }

    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), []);
    }
}
