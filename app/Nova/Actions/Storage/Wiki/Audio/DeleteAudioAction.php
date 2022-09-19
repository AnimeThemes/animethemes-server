<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\DeleteAudioAction as DeleteAudio;
use App\Models\Wiki\Audio;
use App\Nova\Actions\Storage\Base\DeleteAction;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class DeleteAudioAction.
 */
class DeleteAudioAction extends DeleteAction
{
    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.audio.delete.name');
    }

    /**
     * Get the underlying storage action.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, Audio>  $models
     * @return DeleteAudio
     */
    protected function action(ActionFields $fields, Collection $models): DeleteAudio
    {
        $audio = $models->first();

        return new DeleteAudio($audio);
    }
}
