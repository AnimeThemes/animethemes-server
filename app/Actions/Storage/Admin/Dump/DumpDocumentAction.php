<?php

declare(strict_types=1);

namespace App\Actions\Storage\Admin\Dump;

use App\Concerns\Repositories\Admin\ReconcilesDumpRepositories;
use App\Models\Document\Page;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DumpDocumentAction extends DumpAction
{
    use ReconcilesDumpRepositories;

    final public const string FILENAME_PREFIX = 'animethemes-db-dump-document-';

    /**
     * The list of tables to include in the dump.
     */
    protected function allowedTables(): array
    {
        return [
            Page::TABLE,
        ];
    }

    /**
     * The temporary path for the database dump.
     * Note: The dumper library does not support writing to disk, so we have to write to the local filesystem first.
     * Pattern: "animethemes-db-dump-document-{milliseconds from epoch}.sql".
     */
    protected function getDumpFile(): string
    {
        $filesystem = Storage::disk('local');

        return Str::of($filesystem->path(''))
            ->append(DumpDocumentAction::FILENAME_PREFIX)
            ->append(strval(Date::now()->valueOf()))
            ->append('.sql')
            ->__toString();
    }
}
