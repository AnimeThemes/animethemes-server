<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Base\DeleteAction;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Config;

/**
 * Class DeleteVideoAction.
 *
 * @extends DeleteAction<Video>
 */
class DeleteVideoAction extends DeleteAction
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
     * The list of disk names.
     *
     * @return array
     */
    public function disks(): array
    {
        return Config::get(VideoConstants::DISKS_QUALIFIED);
    }

    /**
     * Get the path to delete.
     *
     * @return string
     */
    protected function path(): string
    {
        return $this->model->path();
    }
}
