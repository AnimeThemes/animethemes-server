<?php

namespace Tests\Feature\Console;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class DatabaseDumpTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The Database Dump Command shall output "Database dump '{dumpFile}' has been created".
     *
     * @return void
     */
    public function testDataBaseDumpOutput()
    {
        $dumpFile = $this->getDumpFile();

        $this->artisan('db:dump')->expectsOutput("Database dump '{$dumpFile}' has been created");
    }

    /**
     * The Database Dump Command shall produce a file in the /path/to/project/storage/db-dumps directory.
     *
     * @return void
     */
    public function testDataBaseDumpFile()
    {
        $dumpFile = $this->getDumpFile();

        $this->artisan('db:dump')->run();

        $this->assertFileExists($dumpFile);
    }

    /**
     * The target path for the database dump.
     *
     * @return string
     */
    private function getDumpFile()
    {
        $randomDate = Carbon::parse($this->faker->iso8601());

        Carbon::setTestNow($randomDate);

        $dumpFile = Str::of('db-dumps')
            ->append(DIRECTORY_SEPARATOR)
            ->append('animethemes-db-dump-')
            ->append($randomDate->toDateString())
            ->append('.sql')
            ->__toString();

        return storage_path($dumpFile);
    }
}
