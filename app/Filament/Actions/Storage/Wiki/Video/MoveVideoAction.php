<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\MoveVideoAction as MoveVideo;
use App\Constants\Config\VideoConstants;
use App\Filament\Actions\Storage\Base\MoveAction;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

class MoveVideoAction extends MoveAction
{
    public static function getDefaultName(): ?string
    {
        return 'move-video';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video.move.name'));

        $this->visible(Gate::allows('create', Video::class));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Video  $video
     * @param  array<string, mixed>  $data
     */
    protected function storageAction(?Model $video, array $data): MoveVideo
    {
        /** @var string $path */
        $path = Arr::get($data, 'path');

        return new MoveVideo($video, $path);
    }

    public function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Resolve the default value for the path field.
     */
    protected function defaultPath(): ?string
    {
        $video = $this->getRecord();

        return $video instanceof Video
            ? $video->path
            : null;
    }

    /**
     * The file extension that the path must end with.
     */
    protected function allowedFileExtension(): string
    {
        return '.webm';
    }
}
