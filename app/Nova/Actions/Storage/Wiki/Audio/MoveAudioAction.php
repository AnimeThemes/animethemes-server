<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\MoveAudioAction as MoveAudio;
use App\Constants\Config\AudioConstants;
use App\Models\Wiki\Audio;
use App\Nova\Actions\Storage\Base\MoveAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class MoveAudioAction.
 */
class MoveAudioAction extends MoveAction
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
        return __('nova.actions.audio.move.name');
    }

    /**
     * Get the underlying storage action.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, Audio>  $models
     * @return MoveAudio
     */
    protected function action(ActionFields $fields, Collection $models): MoveAudio
    {
        $path = $fields->get('path');

        $audio = $models->first();

        return new MoveAudio($audio, $path);
    }

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Resolve the default value for the path field.
     *
     * @param  NovaRequest  $request
     * @return string|null
     */
    protected function defaultPath(NovaRequest $request): ?string
    {
        $audio = $request->findModelQuery()->first();

        return $audio instanceof Audio
            ? $audio->path
            : null;
    }

    /**
     * The file extension that the path must end with.
     *
     * @return string
     */
    protected function allowedFileExtension(): string
    {
        return '.ogg';
    }
}
