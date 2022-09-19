<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\MoveVideoAction as MoveVideo;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video;
use App\Nova\Actions\Storage\Base\MoveAction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class MoveVideoAction.
 */
class MoveVideoAction extends MoveAction
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
        return __('nova.actions.video.move.name');
    }

    /**
     * Get the underlying storage action.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, Video>  $models
     * @return MoveVideo
     */
    protected function action(ActionFields $fields, Collection $models): MoveVideo
    {
        $path = $fields->get('path');

        $video = $models->first();

        return new MoveVideo($video, $path);
    }

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Resolve the default value for the path field.
     *
     * @param  NovaRequest  $request
     * @return string|null
     */
    protected function defaultPath(NovaRequest $request): ?string
    {
        $video = $request->findModelQuery()->first();

        return $video instanceof Video
            ? $video->path
            : null;
    }

    /**
     * The file extension that the path must end with.
     *
     * @return string
     */
    protected function allowedFileExtension(): string
    {
        return '.webm';
    }
}
