<?php

declare(strict_types=1);

namespace App\Actions\Storage\Admin\Dump;

use App\Actions\Storage\Base\PruneAction;
use App\Concerns\Repositories\Admin\ReconcilesDumpRepositories;
use App\Constants\Config\DumpConstants;
use App\Models\Admin\Dump;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

/**
 * Class PruneDumpAction.
 */
class PruneDumpAction extends PruneAction
{
    use ReconcilesDumpRepositories;

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(DumpConstants::DISK_QUALIFIED);
    }

    /**
     * Determine whether the file should be pruned.
     *
     * @param  string  $path
     * @param  Carbon  $lastModified
     * @return bool
     */
    protected function shouldBePruned(string $path, Carbon $lastModified): bool
    {
        if (Str::contains($path, Dump::safeDumps())) {
            return true;
        }

        return $lastModified->isBefore(Date::now()->subWeeks(3));
    }
}
