<?php

declare(strict_types=1);

namespace App\Nova\Resources;

use App\Models\BaseModel;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Resource as NovaResource;

/**
 * Class Resource.
 */
abstract class BaseResource extends NovaResource
{
    /**
     * The number of results to display when searching relatable resource without Scout.
     *
     * @var int|null
     */
    public static $relatableSearchResults = 10;

    /**
     * The number of results to display when searching the resource using Scout.
     *
     * @var int
     */
    public static $scoutSearchResults = 10;

    /**
     * The number of resources to show per page via relationships.
     *
     * @var int
     */
    public static $perPageViaRelationship = 10;

    /**
     * The debounce amount to use when searching this resource.
     *
     * @var float
     */
    public static $debounce = 1.0;

    /**
     * The panel for timestamp fields.
     *
     * @return array
     */
    protected function timestamps(): array
    {
        return [
            DateTime::make(__('nova.created_at'), BaseModel::ATTRIBUTE_CREATED_AT)
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable(),

            DateTime::make(__('nova.updated_at'), BaseModel::ATTRIBUTE_UPDATED_AT)
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable(),

            DateTime::make(__('nova.deleted_at'), BaseModel::ATTRIBUTE_DELETED_AT)
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable(),
        ];
    }
}
