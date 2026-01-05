<?php

declare(strict_types=1);

namespace App\Actions\Storage\Admin\Dump;

use App\Concerns\Repositories\Admin\ReconcilesDumpRepositories;
use App\Models\Aggregate\LikeAggregate;
use App\Models\User\Like;
use App\Models\User\Notification;
use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionStage;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DumpUserAction extends DumpAction
{
    use ReconcilesDumpRepositories;

    final public const string FILENAME_PREFIX = 'animethemes-db-dump-user-';

    /**
     * The list of tables to include in the dump.
     */
    public static function allowedTables(): array
    {
        return [
            Like::TABLE,
            LikeAggregate::TABLE,
            Notification::TABLE,
            Submission::TABLE,
            SubmissionStage::TABLE,
        ];
    }

    /**
     * The temporary path for the database dump.
     * Note: The dumper library does not support writing to disk, so we have to write to the local filesystem first.
     * Pattern: "animethemes-db-dump-user-{milliseconds from epoch}.sql".
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
