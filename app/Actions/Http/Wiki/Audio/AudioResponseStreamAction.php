<?php

declare(strict_types=1);

namespace App\Actions\Http\Wiki\Audio;

use App\Actions\Http\ResponseStreamAction;
use App\Constants\Config\AudioConstants;
use App\Models\Wiki\Audio;
use Illuminate\Support\Facades\Config;

/**
 * Class AudioResponseStreamAction.
 */
class AudioResponseStreamAction extends ResponseStreamAction
{
    /**
     * Create a new action instance.
     *
     * @param  Audio  $audio
     */
    public function __construct(Audio $audio)
    {
        parent::__construct($audio);
    }

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED);
    }
}
