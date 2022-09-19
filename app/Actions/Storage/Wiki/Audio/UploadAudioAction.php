<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Base\UploadAction;
use App\Concerns\Repositories\Wiki\ReconcilesAudioRepositories;
use App\Constants\Config\AudioConstants;
use Illuminate\Support\Facades\Config;

/**
 * Class UploadAudioAction.
 */
class UploadAudioAction extends UploadAction
{
    use ReconcilesAudioRepositories;

    /**
     * The list of disk names.
     *
     * @return array
     */
    public function disks(): array
    {
        return Config::get(AudioConstants::DISKS_QUALIFIED);
    }
}
