<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Api\Filter\Base\CreatedAtFilter;
use App\Http\Api\Filter\Base\DeletedAtFilter;
use App\Http\Api\Filter\Base\TrashedFilter;
use App\Http\Api\Filter\Base\UpdatedAtFilter;
use App\Http\Api\Filter\Wiki\ExternalResource\ExternalResourceExternalIdFilter;
use App\Http\Api\Filter\Wiki\ExternalResource\ExternalResourceLinkFilter;
use App\Http\Api\Filter\Wiki\ExternalResource\ExternalResourceSiteFilter;
use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\Request;

/**
 * Class ExternalResourceCollection.
 */
class ExternalResourceCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'resources';

    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = ExternalResource::class;

    /**
     * Transform the resource into a JSON array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->collection->map(function (ExternalResource $resource) {
            return ExternalResourceResource::make($resource, $this->parser);
        })->all();
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedIncludePaths(): array
    {
        return [
            'anime',
            'artists',
        ];
    }

    /**
     * The sort field names a client is allowed to request.
     *
     * @return string[]
     */
    public static function allowedSortFields(): array
    {
        return [
            'resource_id',
            'created_at',
            'updated_at',
            'deleted_at',
            'site',
            'link',
            'external_id',
        ];
    }

    /**
     * The filters that can be applied by the client for this resource.
     *
     * @return string[]
     */
    public static function filters(): array
    {
        return [
            ExternalResourceLinkFilter::class,
            ExternalResourceExternalIdFilter::class,
            ExternalResourceSiteFilter::class,
            CreatedAtFilter::class,
            UpdatedAtFilter::class,
            DeletedAtFilter::class,
            TrashedFilter::class,
        ];
    }
}
