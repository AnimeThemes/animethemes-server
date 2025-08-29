<?php

declare(strict_types=1);

namespace App\Actions\Http\Wiki\Video\Script;

use App\Actions\Http\DownloadAction;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Facades\Config;

/**
 * @extends DownloadAction<VideoScript>
 */
class ScriptDownloadAction extends DownloadAction
{
    public function __construct(VideoScript $script)
    {
        parent::__construct($script);
    }

    /**
     * Get the path of the resource in storage.
     */
    protected function path(): string
    {
        return $this->model->path;
    }

    /**
     * The name of the disk.
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED);
    }
}
