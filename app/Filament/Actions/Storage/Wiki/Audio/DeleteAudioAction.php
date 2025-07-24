<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\DeleteAudioAction as DeleteAudio;
use App\Filament\Actions\Storage\Base\DeleteAction;
use App\Models\Wiki\Audio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class DeleteAudioAction extends DeleteAction
{
    /**
     * The default name of the action.
     */
    public static function getDefaultName(): ?string
    {
        return 'delete-audio';
    }

    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.audio.delete.name'));

        $this->visible(Gate::allows('forceDelete', Audio::class));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Audio  $audio
     * @param  array<string, mixed>  $data
     */
    protected function storageAction(?Model $audio, array $data): DeleteAudio
    {
        return new DeleteAudio($audio);
    }
}
