<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\DeleteVideoAction as DeleteVideo;
use App\Filament\HeaderActions\Storage\Base\DeleteHeaderAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeleteVideoHeaderAction.
 */
class DeleteVideoHeaderAction extends DeleteHeaderAction
{
    /**
     * Get the underlying storage action.
     *
     * @param  Model  $video
     * @param  array  $fields
     * @return DeleteVideo
     */
    protected function storageAction(Model $video, array $fields): DeleteVideo
    {
        return new DeleteVideo($video);
    }
}
