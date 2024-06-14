<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\DeleteAudioAction as DeleteAudio;
use App\Filament\BulkActions\Storage\Base\DeleteBulkAction;
use App\Models\BaseModel;
use App\Models\Wiki\Audio;

/**
 * Class DeleteAudioBulkAction.
 */
class DeleteAudioBulkAction extends DeleteBulkAction
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
