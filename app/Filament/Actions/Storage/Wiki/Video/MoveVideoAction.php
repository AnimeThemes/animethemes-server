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

/**
 * Class MoveVideoAction.
 */
class MoveVideoAction extends MoveAction
{
    /**
     * The default name of the action.
     *
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        return 'move-video';
    }

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video.move.name'));

        $this->authorize('create', Video::class);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Video  $video
     * @param  array  $fields
     * @return MoveVideo
     */
    protected function storageAction(?Model $video, array $fields): MoveVideo
    {
        /** @var string $path */
        $path = Arr::get($fields, 'path');

        return new MoveVideo($video, $path);
    }

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Resolve the default value for the path field.
     *
     * @return string|null
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
     *
     * @return string
     */
    protected function allowedFileExtension(): string
    {
        return '.webm';
    }
}
