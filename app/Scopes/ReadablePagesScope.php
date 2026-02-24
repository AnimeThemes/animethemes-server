<?php

declare(strict_types=1);

namespace App\Scopes;

use App\Enums\Auth\Role as RoleEnum;
use App\Models\Auth\Role;
use App\Models\Document\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class ReadablePagesScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::check()) {
            $builder->whereDoesntHave(Page::RELATION_VIEWER_ROLES);

            return;
        }

        // Bypass for admins.
        if (Auth::user()->hasRole(RoleEnum::ADMIN->value)) {
            return;
        }

        $builder->where(function (Builder $query): void {
            // Public pages.
            $query->whereDoesntHave(Page::RELATION_VIEWER_ROLES);

            // Pages the user can view via their roles.
            $query->orWhereHas(
                Page::RELATION_ROLES,
                fn (Builder $query) => $query->whereIn(
                    new Role()->getQualifiedKeyName(),
                    Auth::user()->roles()->pluck(Role::ATTRIBUTE_ID)
                )
            );
        });
    }
}
