<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\DeleteVideoAction as DeleteVideo;
use App\Filament\Actions\Storage\Base\DeleteAction;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeleteVideoAction.
 */
class DeleteVideoAction extends DeleteAction
{
    /**
     * The default name of the action.
     *
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        return 'delete-video';
    }

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
    protected function storageAction(?Model $video, array $fields): DeleteVideo
    {
        return new DeleteVideo($video);
    }
}
