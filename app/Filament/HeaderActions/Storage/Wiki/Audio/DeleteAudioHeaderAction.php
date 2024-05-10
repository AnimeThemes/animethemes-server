<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\DeleteAudioAction as DeleteAudio;
use App\Filament\HeaderActions\Storage\Base\DeleteHeaderAction;
use App\Models\BaseModel;
use App\Models\Wiki\Audio;

/**
 * Class DeleteAudioHeaderAction.
 */
class DeleteAudioHeaderAction extends DeleteHeaderAction
{
    /**
     * Get the underlying storage action.
     *
     * @param  Audio  $audio
     * @param  array  $fields
     * @return DeleteAudio
     */
    protected function storageAction(BaseModel $audio, array $fields): DeleteAudio
    {
        return new DeleteAudio($audio);
    }
}
