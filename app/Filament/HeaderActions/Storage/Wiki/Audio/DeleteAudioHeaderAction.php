<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\DeleteAudioAction as DeleteAudio;
use App\Filament\HeaderActions\Storage\Base\DeleteHeaderAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeleteAudioHeaderAction.
 */
class DeleteAudioHeaderAction extends DeleteHeaderAction
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
