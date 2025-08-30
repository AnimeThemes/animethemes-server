<?php

declare(strict_types=1);

namespace App\Actions\Http\Wiki\Video;

use App\Actions\Http\ResponseStreamAction;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Config;

class VideoResponseStreamAction extends ResponseStreamAction
{
    public function __construct(Video $video)
    {
        parent::__construct($video);
    }

    public function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }
}
