<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\DeleteVideoAction as DeleteVideo;
use App\Filament\BulkActions\Storage\Base\DeleteBulkAction;
use App\Models\BaseModel;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Auth;

/**
 * Class DeleteVideoBulkAction.
 */
class DeleteVideoBulkAction extends DeleteBulkAction
{
    /**
     * The default name of the action.
     *
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        return 'remove-video-bulk';
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
        $this->icon(__('filament-icons.actions.base.delete'));

        $this->visible(Auth::user()->can('forcedeleteany', Video::class));
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
