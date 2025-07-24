<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Base\MoveAction;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

/**
 * Class MoveVideoAction.
 *
 * @extends MoveAction<VideoScript>
 */
class MoveScriptAction extends MoveAction
{
    public function __construct(VideoScript $script, string $to)
    {
        parent::__construct($script, $to);
    }

    /**
     * The list of disk names.
     *
     * @return array
     */
    public function disks(): array
    {
        return Arr::wrap(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));
    }

    /**
     * Get the path to move from.
     */
    protected function from(): string
    {
        return $this->model->path;
    }

    /**
     * Update underlying model.
     * We want to apply these updates through Eloquent to preserve relations when renaming.
     * Otherwise, reconciliation would destroy the old model and create a new model for the new name.
     */
    protected function update(): VideoScript
    {
        $this->model->update([
            VideoScript::ATTRIBUTE_PATH => $this->to,
        ]);

        return $this->model;
    }
}
