<?php

declare(strict_types=1);

namespace App\Actions\Http;

use App\Contracts\Storage\InteractsWithDisk;
use App\Models\BaseModel;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @template TModel of \App\Models\BaseModel
 */
abstract class DownloadAction implements InteractsWithDisk
{
    /**
     * @param  TModel  $model
     */
    public function __construct(protected readonly BaseModel $model) {}

    public function download(): StreamedResponse
    {
        /** @var FilesystemAdapter $fs */
        $fs = Storage::disk($this->disk());

        return $fs->download($this->path());
    }

    /**
     * Get the path of the resource in storage.
     */
    abstract protected function path(): string;
}
