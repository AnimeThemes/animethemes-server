<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\DeleteVideoAction as DeleteVideo;
use App\Filament\HeaderActions\Storage\Base\DeleteHeaderAction;
use App\Models\BaseModel;
use App\Models\Wiki\Video;

/**
 * Class DeleteVideoHeaderAction.
 */
class DeleteVideoHeaderAction extends DeleteHeaderAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video.delete.name'));

        $this->authorize('forcedelete', Video::class);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Video  $video
     * @param  array  $fields
     * @return DeleteVideo
     */
    protected function storageAction(BaseModel $video, array $fields): DeleteVideo
    {
        return new DeleteVideo($video);
    }
}
