<?php

declare(strict_types=1);

namespace App\Repositories\Storage\Admin;

use App\Constants\Config\DumpConstants;
use App\Models\Admin\Dump;
use App\Repositories\Storage\StorageRepository;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use League\Flysystem\StorageAttributes;

/**
 * @extends StorageRepository<Dump>
 */
class DumpRepository extends StorageRepository
{
    public function disk(): string
    {
        return Config::get(DumpConstants::DISK_QUALIFIED);
    }

    /**
     * Return the callback to filter filesystem contents.
     *
     * @return Closure(StorageAttributes): bool
     */
    protected function filterCallback(): Closure
    {
        return fn (StorageAttributes $file) => $file->isFile() && File::extension($file->path()) === 'sql';
    }

    /**
     * Map filesystem files to model.
     *
     * @return Closure(StorageAttributes): Dump
     */
    protected function mapCallback(): Closure
    {
        return fn (StorageAttributes $file) => new Dump([
            Dump::ATTRIBUTE_PATH => $file->path(),
        ]);
    }
}
