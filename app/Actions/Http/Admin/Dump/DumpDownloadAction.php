<?php

declare(strict_types=1);

namespace App\Actions\Http\Admin\Dump;

use App\Actions\Http\DownloadAction;
use App\Constants\Config\DumpConstants;
use App\Models\Admin\Dump;
use Illuminate\Support\Facades\Config;

/**
 * @extends DownloadAction<Dump>
 */
class DumpDownloadAction extends DownloadAction
{
    /**
     * Get the path of the resource in storage.
     */
    protected function path(): string
    {
        return $this->model->path;
    }

    public function disk(): string
    {
        return Config::get(DumpConstants::DISK_QUALIFIED);
    }
}
