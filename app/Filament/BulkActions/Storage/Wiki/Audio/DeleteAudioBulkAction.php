<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\DeleteAudioAction as DeleteAudio;
use App\Filament\BulkActions\Storage\Base\DeleteBulkAction;
use App\Models\BaseModel;
use App\Models\Wiki\Audio;
use Illuminate\Support\Facades\Auth;

/**
 * Class DeleteAudioBulkAction.
 */
class DeleteAudioBulkAction extends DeleteBulkAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.audio.delete.name'));

        $this->visible(Auth::user()->can('forcedeleteany', Audio::class));
    }

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
