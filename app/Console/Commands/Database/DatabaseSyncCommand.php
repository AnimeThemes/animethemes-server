<?php

declare(strict_types=1);

namespace App\Console\Commands\Database;

use App\Actions\Storage\Admin\Dump\DumpDocumentAction;
use App\Actions\Storage\Admin\Dump\DumpWikiAction;
use App\Console\Commands\BaseCommand;
use Database\Seeders\Admin\Feature\FeatureSeeder;
use Database\Seeders\Auth\Permission\PermissionSeeder;
use Database\Seeders\Auth\Role\RoleSeeder;
use Database\Seeders\Scout\ImportModelsSeeder;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

class DatabaseSyncCommand extends BaseCommand
{
    protected $signature = 'db:sync
        {--drop : Determine whether the existing database should be re-created}';

    protected $description = 'Sync the local database with the latest dumps';

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
            Schema::withoutForeignKeyConstraints(function () {
                foreach ([
                    ...DumpDocumentAction::allowedTables(),
                    ...DumpWikiAction::allowedTables(),
                ] as $table) {
                    $this->info("Truncating table {$table}");
                    DB::table($table)->truncate();
                }
            });
        }

        DB::statement("USE `{$database}`");

        $this->info('Importing wiki dump');
        $wiki = Http::get('https://dump.animethemes.moe/latest/wiki')->body();
        DB::unprepared($wiki);

        $this->info('Importing document dump');
        $document = Http::get('https://dump.animethemes.moe/latest/document')->body();
        DB::unprepared($document);

        $this->info('Migrating database');
        Artisan::call('migrate');

        $this->info('Seeding permissions');
        Artisan::call('db:seed', ['class' => PermissionSeeder::class]);

        $this->info('Seeding roles');
        Artisan::call('db:seed', ['class' => RoleSeeder::class]);

        $this->info('Seeding features');
        Artisan::call('db:seed', ['class' => FeatureSeeder::class]);

        $this->info('Importing models for scout');
        Artisan::call('db:seed', ['class' => ImportModelsSeeder::class]);

        return 0;
    }

    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), []);
    }
}
