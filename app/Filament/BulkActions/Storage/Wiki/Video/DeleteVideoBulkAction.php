<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\DeleteVideoAction as DeleteVideo;
use App\Filament\BulkActions\Storage\Base\DeleteBulkAction;
use App\Models\BaseModel;
use App\Models\Wiki\Video;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;

class DeleteVideoBulkAction extends DeleteBulkAction
{
    /**
     * The default name of the action.
     */
    public static function getDefaultName(): ?string
    {
        return 'remove-video-bulk';
    }

    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video.delete.name'));
        $this->icon(Heroicon::Trash);

        $this->visible(Gate::allows('forceDeleteAny', Video::class));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Video  $video
     * @param  array<string, mixed>  $data
     */
    protected function storageAction(BaseModel $video, array $data): DeleteVideo
    {
        return new DeleteVideo($video);
    }
}
