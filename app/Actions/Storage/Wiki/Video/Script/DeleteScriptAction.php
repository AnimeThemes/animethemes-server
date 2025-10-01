<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Base\DeleteAction;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

/**
 * @extends DeleteAction<VideoScript>
 */
class DeleteScriptAction extends DeleteAction
{
    public function __construct(VideoScript $script)
    {
        parent::__construct($script);
    }

    /**
     * The list of disk names.
     */
    public function disks(): array
    {
        return Arr::wrap(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));
    }

    /**
     * Get the path to delete.
     */
    protected function path(): string
    {
        return $this->model->path;
    }
}
