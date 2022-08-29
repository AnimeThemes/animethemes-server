<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Video;

use App\Actions\Storage\Wiki\Video\MoveVideoAction as MoveVideo;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video;
use App\Rules\Storage\StorageFileDirectoryExistsRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class MoveVideoAction.
 */
class MoveVideoAction extends Action
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
        return __('nova.move_video');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, Video>  $models
     * @return array
     */
    public function handle(ActionFields $fields, Collection $models): array
    {
        $path = $fields->get('path');

        $video = $models->first();

        $action = new MoveVideo($video, $path);

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
        $video = $request->findModelQuery()->first();

        $fs = Storage::disk(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        return [
            Text::make(__('nova.path'), 'path')
                ->required()
                ->rules(['required', 'string', 'doesnt_start_with:/', 'ends_with:.webm', new StorageFileDirectoryExistsRule($fs)])
                ->default(fn () => $video instanceof Video ? $video->path : null)
                ->help(__('nova.move_video_path_help')),
        ];
    }
}
