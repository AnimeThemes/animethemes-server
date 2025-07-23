<?php

declare(strict_types=1);

namespace App\Actions\Http\Admin\Dump;

use App\Actions\Http\DownloadAction;
use App\Constants\Config\DumpConstants;
use App\Models\Admin\Dump;
use Illuminate\Support\Facades\Config;

/**
 * Class DumpDownloadAction.
 *
 * @extends DownloadAction<Dump>
 */
class DumpDownloadAction extends DownloadAction
{
    public function __construct(Dump $dump)
    {
        parent::__construct($dump);
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
        return Config::get(DumpConstants::DISK_QUALIFIED);
    }
}
