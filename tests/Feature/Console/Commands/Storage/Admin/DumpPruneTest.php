<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Storage\Admin;

use App\Actions\Storage\Admin\Dump\DumpWikiAction;
use App\Console\Commands\Storage\Admin\DumpPruneCommand;
use App\Constants\Config\DumpConstants;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DumpPruneTest extends TestCase
{
    use WithFaker;

    /**
     * If no changes are needed, the Prune Database Dumps Command shall output 'No database dumps deleted'.
     */
    public function testNoResults(): void
    {
        Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        $this->artisan(DumpPruneCommand::class, ['--hours' => 0])
            ->assertFailed()
            ->expectsOutput('No prunings were attempted.');
    }

    /**
     * If dumps are deleted, the Prune Database Dumps Command shall output '{Deleted Count} database dumps deleted'.
     */
    public function testDeleted(): void
    {
        Storage::fake(Config::get(DumpConstants::DISK_QUALIFIED));

        $prunedCount = $this->faker->randomDigitNotNull();

        Collection::times($prunedCount, function () {
            Date::setTestNow($this->faker->iso8601());

            $action = new DumpWikiAction();

            $action->handle();
        });

        Date::setTestNow();

        $this->artisan(DumpPruneCommand::class, ['--hours' => -1])
            ->assertSuccessful()
            ->expectsOutputToContain('Pruned');
    }
}
