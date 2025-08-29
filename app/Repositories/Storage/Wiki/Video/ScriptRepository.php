<?php

declare(strict_types=1);

namespace App\Repositories\Storage\Wiki\Video;

use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video\VideoScript;
use App\Repositories\Storage\StorageRepository;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use League\Flysystem\StorageAttributes;

/**
 * @extends StorageRepository<VideoScript>
 */
class ScriptRepository extends StorageRepository
{
    /**
     * The name of the disk.
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED);
    }

    /**
     * Return the callback to filter filesystem contents.
     *
     * @return Closure(StorageAttributes): bool
     */
    protected function filterCallback(): Closure
    {
        return fn (StorageAttributes $file) => $file->isFile() && File::extension($file->path()) === 'txt';
    }

    /**
     * Map filesystem files to model.
     *
     * @return Closure(StorageAttributes): VideoScript
     */
    protected function mapCallback(): Closure
    {
        return fn (StorageAttributes $file) => new VideoScript([
            VideoScript::ATTRIBUTE_PATH => $file->path(),
        ]);
    }
}
