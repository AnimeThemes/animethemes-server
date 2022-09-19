<?php

declare(strict_types=1);

namespace App\Actions\Storage\Admin\Dump;

use App\Actions\Storage\Base\PruneAction;
use App\Concerns\Repositories\Admin\ReconcilesDumpRepositories;
use App\Constants\Config\DumpConstants;
use Illuminate\Support\Facades\Config;

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
}
