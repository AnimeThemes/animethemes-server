<?php

declare(strict_types=1);

namespace App\Actions\Http\Wiki\Audio;

use App\Actions\Http\NginxStreamAction;
use App\Constants\Config\AudioConstants;
use App\Models\Wiki\Audio;
use Illuminate\Support\Facades\Config;

class AudioNginxStreamAction extends NginxStreamAction
{
    public function __construct(Audio $audio)
    {
        parent::__construct($audio);
    }

    /**
     * The name of the disk.
     */
    public function disk(): string
    {
        return Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Get the location of the nginx internal redirect.
     */
    protected function nginxRedirect(): string
    {
        return Config::get(AudioConstants::NGINX_REDIRECT_QUALIFIED);
    }
}
