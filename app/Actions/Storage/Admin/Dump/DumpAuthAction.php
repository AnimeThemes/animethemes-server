<?php

declare(strict_types=1);

namespace App\Actions\Storage\Admin\Dump;

use App\Concerns\Repositories\Admin\ReconcilesDumpRepositories;
use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\List\External\ExternalToken;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DumpAuthAction extends DumpAction
{
    use ReconcilesDumpRepositories;

    final public const FILENAME_PREFIX = 'animethemes-db-dump-auth-';

    /**
     * The list of tables to include in the dump.
     *
     * @return array
     */
    protected function allowedTables(): array
    {
        return [
            // This table stores tokens which are sensitive data.
            ExternalToken::TABLE,

            Permission::TABLE,
            Role::TABLE,
            User::TABLE,
            'model_has_permissions',
            'model_has_roles',
            'password_reset_tokens',
            'personal_access_tokens',
            'role_has_permissions',
        ];
    }

    /**
     * The temporary path for the database dump.
     * Note: The dumper library does not support writing to disk, so we have to write to the local filesystem first.
     * Pattern: "animethemes-db-dump-auth-{milliseconds from epoch}.sql".
     */
    protected function getDumpFile(): string
    {
        $filesystem = Storage::disk('local');

        return Str::of($filesystem->path(''))
            ->append(DumpAuthAction::FILENAME_PREFIX)
            ->append(strval(Date::now()->valueOf()))
            ->append('.sql')
            ->__toString();
    }
}
