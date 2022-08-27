<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Video;

use App\Actions\Storage\Wiki\Video\DeleteVideoAction as DeleteVideo;
use App\Models\Wiki\Video;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\DestructiveAction;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class DeleteVideoAction.
 */
class DeleteVideoAction extends DestructiveAction
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
        return __('nova.remove_video');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, Video>  $models
     * @return array
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(ActionFields $fields, Collection $models): array
    {
        $video = $models->first();

        $action = new DeleteVideo($video);

        $storageResults = $action->handle();

        $storageResults->toLog();

        $result = $storageResults->toActionResult();

        if ($result->hasFailed()) {
            return Action::danger($result->getMessage());
        }

        return Action::message($result->getMessage());
    }
}
