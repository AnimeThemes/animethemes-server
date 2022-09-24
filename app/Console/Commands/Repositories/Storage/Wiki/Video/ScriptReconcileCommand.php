<?php

declare(strict_types=1);

namespace App\Console\Commands\Repositories\Storage\Wiki\Video;

use App\Concerns\Repositories\Wiki\Video\ReconcilesScriptRepositories;
use App\Console\Commands\Repositories\Storage\StorageReconcileCommand;
use App\Constants\Config\VideoConstants;
use Illuminate\Support\Facades\Config;

/**
 * Class ScriptReconcileCommand.
 */
class ScriptReconcileCommand extends StorageReconcileCommand
{
    use ReconcilesScriptRepositories;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reconcile:script
                                {--path= : The directory of scripts to reconcile. Ex: 2022/Spring/. If unspecified, all directories will be listed.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform set reconciliation between object storage and script database';

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED);
    }
}
