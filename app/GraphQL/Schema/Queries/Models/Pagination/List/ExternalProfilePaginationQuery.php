<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries\Models\Pagination\List;

use App\Enums\Models\List\ExternalProfileVisibility;
use App\GraphQL\Schema\Queries\Models\Pagination\EloquentPaginationQuery;
use App\GraphQL\Schema\Types\List\ExternalProfileType;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use App\Models\List\ExternalProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ExternalProfilePaginationQuery extends EloquentPaginationQuery
{
    protected $middleware = [
        EnabledOnlyOnLocalhost::class,
    ];

    public function __construct()
    {
        parent::__construct('externalprofilePagination');
    }

    public function description(): string
    {
        return 'Returns a listing of external profile resources given fields.';
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): ExternalProfileType
    {
        return new ExternalProfileType();
    }

    protected function query(Builder $builder, array $args): Builder
    {
        $builder->where(ExternalProfile::ATTRIBUTE_VISIBILITY, ExternalProfileVisibility::PUBLIC->value);

        if ($user = Auth::user()) {
            return $builder->orWhereBelongsTo($user, ExternalProfile::RELATION_USER);
        }

        return $builder;
    }
}
