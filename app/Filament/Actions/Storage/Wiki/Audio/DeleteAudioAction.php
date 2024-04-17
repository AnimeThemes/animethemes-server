<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\DeleteAudioAction as DeleteAudio;
use App\Filament\Actions\Storage\Base\DeleteAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeleteAudioAction.
 */
class DeleteAudioAction extends DeleteAction
{
    /**
     * Get the underlying storage action.
     *
     * @param  Model  $audio
     * @param  array  $fields
     * @return DeleteAudio
     */
    protected function storageAction(Model $audio, array $fields): DeleteAudio
    {
        return new DeleteAudio($audio);
    }
}
