<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Storage\Admin;

use App\Console\Commands\Storage\Admin\DocumentDumpCommand;
use App\Constants\Config\DumpConstants;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class DocumentDumpTest.
 */
class DocumentDumpTest extends TestCase
{
    use WithFaker;

    /**
     * The Database Dump Command shall output "Database dump '{dumpFile}' has been created".
     *
     * @return void
     */
    public function testDataBaseDumpOutput(): void
    {
        $this->baseRefreshDatabase(); // Cannot lazily refresh database within pending command

        Storage::fake('local');
        Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        Date::setTestNow($this->faker->iso8601());

        $this->artisan(DocumentDumpCommand::class)
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
        $this->baseRefreshDatabase(); // Cannot lazily refresh database within pending command

        $local = Storage::fake('local');
        $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        Date::setTestNow($this->faker->iso8601());

        $this->artisan(DocumentDumpCommand::class)->run();

        static::assertEmpty($local->allFiles());
        static::assertCount(1, $fs->allFiles());
    }
}
