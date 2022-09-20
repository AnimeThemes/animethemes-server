<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Storage\Admin\Dump;

use App\Actions\Storage\Admin\Dump\DumpWikiAction;
use App\Actions\Storage\Admin\Dump\PruneDumpAction;
use App\Constants\Config\DumpConstants;
use App\Enums\Actions\ActionStatus;
use App\Models\Admin\Dump;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class PruneDumpTest.
 */
class PruneDumpTest extends TestCase
{
    use WithFaker;

    /**
     * If no changes are needed, the Prune Dumps Action shall fail.
     *
     * @return void
     */
    public function testNoResults(): void
    {
        $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        $action = new PruneDumpAction($this->faker->numberBetween(2, 9));

        $pruneResults = $action->handle();

        $result = $pruneResults->toActionResult();

        static::assertEmpty($fs->allFiles());
        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(Dump::class, 0);
    }

    /**
     * The Prune Dumps Action shall prune dumps before the specified date by hours from the present time.
     *
     * @return void
     */
    public function testPruned(): void
    {
        $fs = Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        $prunedCount = $this->faker->randomDigitNotNull();

        Collection::times($prunedCount, function () {
            Date::setTestNow($this->faker->iso8601());

            $action = new DumpWikiAction();

            $action->handle();
        });

        Date::setTestNow();

        $action = new PruneDumpAction(-1);

        $pruneResults = $action->handle();

        $action->then($pruneResults);

        $result = $pruneResults->toActionResult();

        static::assertEmpty($fs->allFiles());
        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));

        $dumps = Dump::withTrashed()->get();
        foreach ($dumps as $dump) {
            static::assertSoftDeleted($dump);
        }
    }
}
