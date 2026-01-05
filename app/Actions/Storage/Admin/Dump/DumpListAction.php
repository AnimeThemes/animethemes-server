<?php

declare(strict_types=1);

namespace App\Actions\Storage\Admin\Dump;

use App\Concerns\Repositories\Admin\ReconcilesDumpRepositories;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DumpListAction extends DumpAction
{
    use ReconcilesDumpRepositories;

    final public const string FILENAME_PREFIX = 'animethemes-db-dump-list-';

    /**
     * The list of tables to include in the dump.
     */
    public static function allowedTables(): array
    {
        return [
            ExternalEntry::TABLE,
            ExternalProfile::TABLE,
            PlaylistTrack::TABLE,
            Playlist::TABLE,
        ];
    }

    /**
     * The temporary path for the database dump.
     * Note: The dumper library does not support writing to disk, so we have to write to the local filesystem first.
     * Pattern: "animethemes-db-dump-list-{milliseconds from epoch}.sql".
     */
    protected function getDumpFile(): string
    {
        $filesystem = Storage::disk('local');

        return Str::of($filesystem->path(''))
            ->append(DumpListAction::FILENAME_PREFIX)
            ->append(strval(Date::now()->valueOf()))
            ->append('.sql')
            ->__toString();
    }
}
