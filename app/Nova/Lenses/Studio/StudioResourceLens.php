<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Studio;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Nova\Actions\Wiki\Studio\AttachStudioResourceAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Class StudioResourceLens.
 */
abstract class StudioResourceLens extends StudioLens
{
    /**
     * The resource site.
     *
     * @return ResourceSite
     */
    abstract protected static function site(): ResourceSite;

    /**
     * Get the displayable name of the lens.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.studio_resource_lens', ['site' => static::site()->description]);
    }

    /**
     * The criteria used to refine the models for the lens.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public static function criteria(Builder $query): Builder
    {
        return $query->whereDoesntHave(Studio::RELATION_RESOURCES, function (Builder $resourceQuery) {
            $resourceQuery->where(ExternalResource::ATTRIBUTE_SITE, static::site()->value);
        });
    }

    /**
     * Get the actions available on the lens.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function actions(Request $request): array
    {
        return [
            (new AttachStudioResourceAction(static::site()))
                ->confirmButtonText(__('nova.attach'))
                ->cancelButtonText(__('nova.cancel'))
                ->showInline()
                ->canSee(function (Request $request) {
                    $user = $request->user();

                    return $user instanceof User && $user->can('create external resource');
                }),
        ];
    }
}
