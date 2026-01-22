<?php

declare(strict_types=1);

namespace App\Models\Auth;

use App\Contracts\Models\Nameable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Kyrch\Prohibition\Models\Sanction as BaseSanction;

class Sanction extends BaseSanction implements Nameable
{
    final public const string TABLE = 'sanctions';

    final public const string ATTRIBUTE_ID = 'id';
    final public const string ATTRIBUTE_NAME = 'name';

    final public const string RELATION_PROHIBITIONS = 'prohibitions';
    final public const string RELATION_USERS = 'users';

    public function getName(): string
    {
        return $this->name;
    }

    #[Scope]
    protected function expired(Builder $query): Builder
    {
        $tableName = Config::string('prohibition.table_names.model_sanctions');

        return $query->where("$tableName.expires_at", '<', now());
    }

    #[Scope]
    protected function notExpired(Builder $query): Builder
    {
        $tableName = Config::string('prohibition.table_names.model_sanctions');

        return $query->where("$tableName.expires_at", '>', now())
            ->orWhereNull("$tableName.expires_at");
    }
}
