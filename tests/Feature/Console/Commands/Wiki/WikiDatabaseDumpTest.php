<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Wiki;

use App\Console\Commands\DatabaseDumpCommand;
use App\Console\Commands\Wiki\WikiDatabaseDumpCommand;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class WikiDatabaseDumpTest.
 */
class WikiDatabaseDumpTest extends TestCase
{
    use WithFaker;

    /**
     * The Database Dump Command shall output "Database dump '{dumpFile}' has been created".
     *
     * @return void
     */
    public function testDataBaseDumpOutput(): void
    {
        if (! DatabaseDumpCommand::canIncludeTables(DB::connection())) {
            static::markTestSkipped('DB connection does not support includeTables option');
        }

        Storage::fake('db-dumps');

        Date::setTestNow($this->faker->iso8601());

        $this->artisan(WikiDatabaseDumpCommand::class, ['--create' => $this->faker->boolean()])
            ->assertSuccessful()
            ->expectsOutputToContain('has been created');
    }

    /**
     * The Database Dump Command shall produce a file in the /path/to/project/storage/db-dumps directory.
     *
     * @return void
     */
    public function testDataBaseDumpFile(): void
    {
        if (! DatabaseDumpCommand::canIncludeTables(DB::connection())) {
            static::markTestSkipped('DB connection does not support includeTables option');
        }

        Storage::fake('db-dumps');

        Date::setTestNow($this->faker->iso8601());

        $this->artisan(WikiDatabaseDumpCommand::class, ['--create' => $this->faker->boolean()])->run();

        static::assertCount(1, Storage::disk('db-dumps')->allFiles());
    }
}
