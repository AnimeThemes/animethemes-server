<?php

declare(strict_types=1);

namespace App\GraphQL\Builders\List;

use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\List\ExternalProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ExternalProfilePaginationBuilder
{
    /**
     * @param  array<string, mixed>  $args
     */
    public function __invoke(Builder $builder, null $_, null $root, $args): Builder
    {
        $builder->where(ExternalProfile::ATTRIBUTE_VISIBILITY, ExternalProfileVisibility::PUBLIC->value);

        if ($user = Auth::user()) {
            return $builder->orWhereBelongsTo($user, ExternalProfile::RELATION_USER);
        }

        return $builder;
    }
}
