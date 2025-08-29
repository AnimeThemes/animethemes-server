<?php

declare(strict_types=1);

namespace App\Actions\Http\Wiki\Audio;

use App\Actions\Http\ResponseStreamAction;
use App\Constants\Config\AudioConstants;
use App\Models\Wiki\Audio;
use Illuminate\Support\Facades\Config;

class AudioResponseStreamAction extends ResponseStreamAction
{
    public function __construct(Audio $audio)
    {
        parent::__construct($audio);
    }

    public function disk(): string
    {
        return Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED);
    }
}
