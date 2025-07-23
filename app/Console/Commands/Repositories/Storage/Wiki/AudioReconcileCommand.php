<?php

declare(strict_types=1);

namespace App\Console\Commands\Repositories\Storage\Wiki;

use App\Concerns\Repositories\Wiki\ReconcilesAudioRepositories;
use App\Console\Commands\Repositories\Storage\StorageReconcileCommand;
use App\Constants\Config\AudioConstants;
use Illuminate\Support\Facades\Config;

class AudioReconcileCommand extends StorageReconcileCommand
{
    use ReconcilesAudioRepositories;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'reconcile:audio
                                {--path= : The directory of audios to reconcile. Ex: 2022/Spring/. If unspecified, all directories will be listed.}';

    /**
     * The console command description.
     */
    protected $description = 'Perform set reconciliation between object storage and audios database';

    /**
     * The name of the disk.
     */
    public function disk(): string
    {
        return Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED);
    }
}
