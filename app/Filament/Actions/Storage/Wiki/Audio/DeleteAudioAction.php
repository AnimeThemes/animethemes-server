<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\DeleteAudioAction as DeleteAudio;
use App\Filament\Actions\Storage\Base\DeleteAction;
use App\Models\Wiki\Audio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

/**
 * Class DeleteAudioAction.
 */
class DeleteAudioAction extends DeleteAction
{
    /**
     * The default name of the action.
     *
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        return 'delete-audio';
    }

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.audio.delete.name'));

        $this->visible(Gate::allows('forcedelete', Audio::class));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Audio  $audio
     * @param  array  $fields
     * @return DeleteAudio
     */
    protected function storageAction(?Model $audio, array $fields): DeleteAudio
    {
        return new DeleteAudio($audio);
    }
}
