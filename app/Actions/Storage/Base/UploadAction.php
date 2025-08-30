<?php

declare(strict_types=1);

namespace App\Actions\Storage\Base;

use App\Contracts\Actions\Storage\StorageAction;
use App\Contracts\Actions\Storage\StorageResults;
use App\Contracts\Storage\InteractsWithDisks;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

abstract class UploadAction implements InteractsWithDisks, StorageAction
{
    public function __construct(protected readonly UploadedFile $file, protected readonly string $path) {}

    public function handle(): StorageResults
    {
        $results = [];

        foreach ($this->disks() as $disk) {
            /** @var FilesystemAdapter $fs */
            $fs = Storage::disk($disk);

            $result = $fs->putFileAs($this->path, $this->file, $this->file->getClientOriginalName());

            $results[$disk] = $result;
        }

        return new UploadResults($results);
    }
}
