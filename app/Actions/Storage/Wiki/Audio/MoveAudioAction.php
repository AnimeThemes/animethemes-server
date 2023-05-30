<?php

declare(strict_types=1);

namespace App\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Base\MoveAction;
use App\Constants\Config\AudioConstants;
use App\Models\Wiki\Audio;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

/**
 * Class MoveAudioAction.
 *
 * @extends MoveAction<Audio>
 */
class MoveAudioAction extends MoveAction
{
    /**
     * Create a new action instance.
     *
     * @param  Audio  $audio
     * @param  string  $to
     */
    public function __construct(Audio $audio, string $to)
    {
        parent::__construct($audio, $to);
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
     * Get the path to move from.
     *
     * @return string
     */
    protected function from(): string
    {
        return $this->model->path();
    }

    /**
     * Update underlying model.
     * We want to apply these updates through Eloquent to preserve relations when renaming.
     * Otherwise, reconciliation would destroy the old model and create a new model for the new name.
     *
     * @return Audio
     */
    protected function update(): Audio
    {
        $this->model->update([
            Audio::ATTRIBUTE_BASENAME => File::basename($this->to),
            Audio::ATTRIBUTE_FILENAME => File::name($this->to),
            Audio::ATTRIBUTE_PATH => $this->to,
        ]);

        return $this->model;
    }
}
