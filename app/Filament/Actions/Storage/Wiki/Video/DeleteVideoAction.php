<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\DeleteVideoAction as DeleteVideo;
use App\Filament\Actions\Storage\Base\DeleteAction;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class DeleteVideoAction extends DeleteAction
{
    public static function getDefaultName(): ?string
    {
        return 'delete-video';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video.delete.name'));

        $this->visible(Gate::allows('forceDeleteAny', Video::class));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Video  $video
     * @param  array<string, mixed>  $data
     */
    protected function storageAction(?Model $video, array $data): DeleteVideo
    {
        return new DeleteVideo($video);
    }
}
