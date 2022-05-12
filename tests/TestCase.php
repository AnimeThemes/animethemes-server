<?php

declare(strict_types=1);

namespace Tests;

use Database\Seeders\PermissionSeeder;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\PermissionRegistrar;

/**
 * Class TestCase.
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use LazilyRefreshDatabase;

    /**
     * Perform any work that should take place once the database has finished refreshing.
     *
     * @return void
     */
    protected function afterRefreshingDatabase(): void
    {
        Artisan::call(SeedCommand::class, ['--class' => PermissionSeeder::class]);
        App::make(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
