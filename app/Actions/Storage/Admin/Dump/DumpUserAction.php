<?php

declare(strict_types=1);

namespace App\Actions\Storage\Admin\Dump;

use App\Concerns\Repositories\Admin\ReconcilesDumpRepositories;
use App\Models\User\Encode;
use App\Models\User\Notification;
use App\Models\User\Report;
use App\Models\User\Report\ReportStep;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class DumpUserAction.
 */
class DumpUserAction extends DumpAction
{
    use ReconcilesDumpRepositories;

    final public const FILENAME_PREFIX = 'animethemes-db-dump-user-';

    /**
     * The list of tables to include in the dump.
     *
     * @return array
     */
    protected function allowedTables(): array
    {
        return [
            Encode::TABLE,
            Notification::TABLE,
            Report::TABLE,
            ReportStep::TABLE,
        ];
    }

    /**
     * The temporary path for the database dump.
     * Note: The dumper library does not support writing to disk, so we have to write to the local filesystem first.
     * Pattern: "animethemes-db-dump-user-{milliseconds from epoch}.sql".
     *
     * @return string
     */
    protected function getDumpFile(): string
    {
        $filesystem = Storage::disk('local');

        return Str::of($filesystem->path(''))
            ->append(DumpUserAction::FILENAME_PREFIX)
            ->append(strval(Date::now()->valueOf()))
            ->append('.sql')
            ->__toString();
    }
}
