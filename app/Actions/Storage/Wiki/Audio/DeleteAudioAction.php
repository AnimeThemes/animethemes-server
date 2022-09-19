<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Base\DeleteAction;
use App\Concerns\Repositories\Wiki\ReconcilesAudioRepositories;
use App\Constants\Config\AudioConstants;
use App\Models\Wiki\Audio;
use Illuminate\Support\Facades\Config;

/**
 * Class DeleteAudioAction.
 *
 * @extends DeleteAction<Audio>
 */
class DeleteAudioAction extends DeleteAction
{
    use ReconcilesAudioRepositories;

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
     * The list of disk names.
     *
     * @return array
     */
    public function disks(): array
    {
        return Config::get(AudioConstants::DISKS_QUALIFIED);
    }

    /**
     * Get the path to delete.
     *
     * @return string
     */
    protected function path(): string
    {
        return $this->model->path();
    }
}
