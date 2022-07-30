<?php

declare(strict_types=1);

namespace App\Repositories\Storage\Wiki;

use App\Models\Wiki\Audio;
use App\Repositories\Storage\StorageRepository;
use Closure;
use Illuminate\Http\Testing\MimeType;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use League\Flysystem\StorageAttributes;

/**
 * Class AudioRepository.
 *
 * @extends StorageRepository<Audio>
 */
class AudioRepository extends StorageRepository
{
    /**
     * Get the name of the disk that represents the filesystem.
     *
     * @return string
     */
    protected function disk(): string
    {
        return Config::get('audio.disk');
    }

    /**
     * Return the callback to filter filesystem contents.
     *
     * @return Closure(StorageAttributes): bool
     */
    protected function filterCallback(): Closure
    {
        return fn (StorageAttributes $file) => $file->isFile() && File::extension($file->path()) === 'ogg';
    }

    /**
     * Map filesystem files to model.
     *
     * @return Closure(StorageAttributes): Audio
     */
    protected function mapCallback(): Closure
    {
        return fn (StorageAttributes $file) => new Audio([
            Audio::ATTRIBUTE_BASENAME => File::basename($file->path()),
            Audio::ATTRIBUTE_FILENAME => File::name($file->path()),
            Audio::ATTRIBUTE_MIMETYPE => MimeType::from($file->path()),
            Audio::ATTRIBUTE_PATH => $file->path(),
            Audio::ATTRIBUTE_SIZE => $file->offsetGet(StorageAttributes::ATTRIBUTE_FILE_SIZE),
        ]);
    }
}
