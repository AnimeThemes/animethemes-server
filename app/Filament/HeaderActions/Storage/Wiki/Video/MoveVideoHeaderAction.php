<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage\Wiki\Video;

use App\Actions\Storage\Wiki\Video\MoveVideoAction as MoveVideo;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video;
use App\Filament\HeaderActions\Storage\Base\MoveHeaderAction;
use App\Models\BaseModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

/**
 * Class MoveVideoHeaderAction.
 */
class MoveVideoHeaderAction extends MoveHeaderAction
{
    /**
     * Get the underlying storage action.
     *
     * @param  Video  $video
     * @param  array  $fields
     * @return MoveVideo
     */
    protected function storageAction(BaseModel $video, array $fields): MoveVideo
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
