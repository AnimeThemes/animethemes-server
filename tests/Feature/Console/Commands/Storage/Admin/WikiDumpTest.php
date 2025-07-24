<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Storage\Admin;

use App\Console\Commands\Storage\Admin\WikiDumpCommand;
use App\Constants\Config\DumpConstants;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WikiDumpTest extends TestCase
{
    use WithFaker;

    /**
     * The Database Dump Command shall output "Database dump '{dumpFile}' has been created".
     */
    public function testDataBaseDumpOutput(): void
    {
        Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        Date::setTestNow($this->faker->iso8601());

        $this->artisan(WikiDumpCommand::class)
            ->assertSuccessful()
            ->expectsOutputToContain('has been created');
    }

    /**
     * The Database Dump Command shall produce a file in the /path/to/project/storage/db-dumps directory.
     */
    public function testDataBaseDumpFile(): void
    {
        $local = Storage::fake('local');
        $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        Date::setTestNow($this->faker->iso8601());

        $this->artisan(WikiDumpCommand::class)->run();

        static::assertEmpty($local->allFiles());
        static::assertCount(1, $fs->allFiles());
    }
}
