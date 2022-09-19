<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Base\UploadAction;
use App\Concerns\Repositories\Wiki\ReconcilesVideoRepositories;
use App\Constants\Config\VideoConstants;
use Illuminate\Support\Facades\Config;

/**
 * Class UploadVideoAction.
 */
class UploadVideoAction extends UploadAction
{
    use ReconcilesVideoRepositories;

    /**
     * The list of disk names.
     *
     * @return array
     */
    public function disks(): array
    {
        return Config::get(VideoConstants::DISKS_QUALIFIED);
    }
}
