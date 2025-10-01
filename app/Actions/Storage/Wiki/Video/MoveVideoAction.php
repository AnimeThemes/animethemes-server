<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Base\MoveAction;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

/**
 * @extends MoveAction<Video>
 */
class MoveVideoAction extends MoveAction
{
    public function __construct(Video $video, string $to)
    {
        parent::__construct($video, $to);
    }

    /**
     * The list of disk names.
     */
    public function disks(): array
    {
        return Config::get(VideoConstants::DISKS_QUALIFIED);
    }

    /**
     * Get the path to move from.
     */
    protected function from(): string
    {
        return $this->model->path();
    }

    /**
     * Update underlying model.
     * We want to apply these updates through Eloquent to preserve relations when renaming.
     * Otherwise, reconciliation would destroy the old model and create a new model for the new name.
     */
    protected function update(): Video
    {
        $this->model->update([
            Video::ATTRIBUTE_BASENAME => File::basename($this->to),
            Video::ATTRIBUTE_FILENAME => File::name($this->to),
            Video::ATTRIBUTE_PATH => $this->to,
        ]);

        return $this->model;
    }
}
