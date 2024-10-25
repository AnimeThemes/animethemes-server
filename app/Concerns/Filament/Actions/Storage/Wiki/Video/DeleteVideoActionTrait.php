<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\DeleteVideoAction as DeleteVideo;
use App\Models\BaseModel;
use App\Models\Wiki\Video;

/**
 * Trait DeleteVideoActionTrait.
 */
trait DeleteVideoActionTrait
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
