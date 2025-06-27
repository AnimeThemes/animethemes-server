<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Storage\Admin\Dump;

use App\Actions\Storage\Admin\Dump\DumpWikiAction;
use App\Constants\Config\DumpConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Admin\Dump;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class DumpWikiTest.
 */
class DumpWikiTest extends TestCase
{
    use WithFaker;

    /**
     * The Database Dump Command shall output "Database dump '{dumpFile}' has been created".
     *
     * @return void
     *
     * @throws Exception
     */
    public function testDataBaseDumpOutput(): void
    {
        $local = Storage::fake('local');
        $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        Date::setTestNow($this->faker->iso8601());

        $action = new DumpWikiAction();

        $result = $action->handle();

        static::assertTrue($result->getStatus() === ActionStatus::PASSED);
        static::assertEmpty($local->allFiles());
        static::assertCount(1, $fs->allFiles());
        static::assertDatabaseCount(Dump::class, 1);
    }
}
