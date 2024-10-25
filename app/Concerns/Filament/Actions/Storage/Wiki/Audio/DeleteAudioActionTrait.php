<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\DeleteAudioAction as DeleteAudio;
use App\Models\BaseModel;
use App\Models\Wiki\Audio;

/**
 * Trait DeleteAudioActionTrait.
 */
trait DeleteAudioActionTrait
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

        $this->authorize('forcedelete', Audio::class);
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
