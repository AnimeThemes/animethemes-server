<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Base\DeleteAction;
use App\Concerns\Repositories\Wiki\Video\ReconcilesScriptRepositories;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

/**
 * Class DeleteScriptAction.
 *
 * @extends DeleteAction<VideoScript>
 */
class DeleteScriptAction extends DeleteAction
{
    use ReconcilesScriptRepositories;

    /**
     * Create a new action instance.
     *
     * @param  VideoScript  $script
     */
    public function __construct(VideoScript $script)
    {
        parent::__construct($script);
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
     * Get the path to delete.
     *
     * @return string
     */
    protected function path(): string
    {
        return $this->model->path;
    }
}
