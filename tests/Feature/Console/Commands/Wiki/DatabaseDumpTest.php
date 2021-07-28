<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Wiki;

use App\Console\Commands\Wiki\DatabaseDumpCommand;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class DatabaseDumpTest.
 */
class DatabaseDumpTest extends TestCase
{
    use WithFaker;

    /**
     * The Database Dump Command shall output "Database dump '{dumpFile}' has been created".
     *
     * @return void
     */
    public function testDataBaseDumpOutput()
    {
        Storage::fake('db-dumps');

        $create = $this->faker->boolean();

        $command = Str::of('db:dump');
        if ($create) {
            $command = $command->append(' --create');
        }

        Carbon::setTestNow($this->faker->iso8601());

        $dumpFile = DatabaseDumpCommand::getDumpFile($create);

        $this->artisan($command->__toString())->expectsOutput("Database dump '{$dumpFile}' has been created");
    }

    /**
     * The Database Dump Command shall produce a file in the /path/to/project/storage/db-dumps directory.
     *
     * @return void
     */
    public function testDataBaseDumpFile()
    {
        Storage::fake('db-dumps');

        $create = $this->faker->boolean();

        $command = Str::of('db:dump');
        if ($create) {
            $command = $command->append(' --create');
        }

        Carbon::setTestNow($this->faker->iso8601());

        $dumpFile = DatabaseDumpCommand::getDumpFile($create);

        $this->artisan($command->__toString())->run();

        static::assertFileExists($dumpFile);
    }
}
