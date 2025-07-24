<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\DeleteAudioAction as DeleteAudio;
use App\Filament\BulkActions\Storage\Base\DeleteBulkAction;
use App\Models\BaseModel;
use App\Models\Wiki\Audio;
use Illuminate\Support\Facades\Gate;

class DeleteAudioBulkAction extends DeleteBulkAction
{
    /**
     * The default name of the action.
     */
    public static function getDefaultName(): ?string
    {
        return 'remove-audio-bulk';
    }

    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.audio.delete.name'));

        $this->visible(Gate::allows('forceDeleteAny', Audio::class));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Audio  $audio
     * @param  array<string, mixed>  $data
     */
    protected function storageAction(BaseModel $audio, array $data): DeleteAudio
    {
        return new DeleteAudio($audio);
    }
}
