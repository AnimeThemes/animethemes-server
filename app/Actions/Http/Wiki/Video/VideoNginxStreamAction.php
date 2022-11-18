<?php

declare(strict_types=1);

namespace App\Actions\Http\Wiki\Video;

use App\Actions\Http\NginxStreamAction;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Config;

/**
 * Class VideoNginxStreamAction.
 */
class VideoNginxStreamAction extends NginxStreamAction
{
    /**
     * Create a new action instance.
     *
     * @param  Video  $video
     */
    public function __construct(Video $video)
    {
        parent::__construct($video);
    }

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Get the location of the nginx internal redirect.
     *
     * @return string
     */
    protected function nginxRedirect(): string
    {
        return Config::get(VideoConstants::NGINX_REDIRECT_QUALIFIED);
    }
}
