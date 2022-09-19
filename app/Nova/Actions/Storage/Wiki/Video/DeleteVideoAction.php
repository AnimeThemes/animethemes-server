<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\DeleteVideoAction as DeleteVideo;
use App\Models\Wiki\Video;
use App\Nova\Actions\Storage\Base\DeleteAction;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class DeleteVideoAction.
 */
class DeleteVideoAction extends DeleteAction
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
        return __('nova.actions.video.delete.name');
    }

    /**
     * Get the underlying storage action.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, Video>  $models
     * @return DeleteVideo
     */
    protected function action(ActionFields $fields, Collection $models): DeleteVideo
    {
        $video = $models->first();

        return new DeleteVideo($video);
    }
}
