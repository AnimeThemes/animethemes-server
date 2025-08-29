<?php

declare(strict_types=1);

namespace App\Repositories\Storage\Wiki;

use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video;
use App\Repositories\Storage\StorageRepository;
use Closure;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use League\Flysystem\StorageAttributes;

/**
 * @extends StorageRepository<Video>
 */
class VideoRepository extends StorageRepository
{
    public function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Return the callback to filter filesystem contents.
     *
     * @return Closure(StorageAttributes): bool
     */
    protected function filterCallback(): Closure
    {
        return fn (StorageAttributes $file) => $file->isFile() && File::extension($file->path()) === 'webm';
    }

    /**
     * Map filesystem files to model.
     *
     * @return Closure(StorageAttributes): Video
     */
    protected function mapCallback(): Closure
    {
        return fn (StorageAttributes $file) => new Video([
            Video::ATTRIBUTE_BASENAME => File::basename($file->path()),
            Video::ATTRIBUTE_FILENAME => File::name($file->path()),
            Video::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
            Video::ATTRIBUTE_PATH => $file->path(),
            Video::ATTRIBUTE_SIZE => $file->offsetGet(StorageAttributes::ATTRIBUTE_FILE_SIZE),
        ]);
    }
}
