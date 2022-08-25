<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\MoveAudioAction as MoveAudio;
use App\Constants\Config\AudioConstants;
use App\Models\Wiki\Audio;
use App\Rules\Storage\StorageFileDirectoryExistsRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class MoveAudioAction.
 */
class MoveAudioAction extends Action
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
        return __('nova.move_audio');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, Audio>  $models
     * @return array
     */
    public function handle(ActionFields $fields, Collection $models): array
    {
        $path = $fields->get('path');

        $audio = $models->first();

        $action = new MoveAudio($audio, $path);

        $storageResults = $action->handle();

        $storageResults->toLog();

        $result = $storageResults->toActionResult();

        if ($result->hasFailed()) {
            return Action::danger($result->getMessage());
        }

        return Action::message($result->getMessage());
    }

    /**
     * Get the fields available on the action.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(NovaRequest $request): array
    {
        $audio = $request->resourceId !== null
            ? $request->findModel()
            : null;

        $fs = Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        return [
            Text::make(__('nova.path'), 'path')
                ->required()
                ->rules(['required', 'string', 'doesnt_start_with:/', 'ends_with:.ogg', new StorageFileDirectoryExistsRule($fs)])
                ->default(fn () => $audio instanceof Audio ? $audio->path : null)
                ->help(__('nova.move_audio_path_help')),
        ];
    }
}
