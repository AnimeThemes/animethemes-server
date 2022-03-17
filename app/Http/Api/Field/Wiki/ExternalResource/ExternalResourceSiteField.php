<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\ExternalResource;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Field\EnumField;
use App\Models\Wiki\ExternalResource;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;

/**
 * Class ExternalResourceSiteColumn.
 */
class ExternalResourceSiteColumn extends EnumField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalResource::ATTRIBUTE_SITE, ResourceSite::class);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            new EnumValue(ResourceSite::class),
        ];
    }

    /**
     * Set the update validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            new EnumValue(ResourceSite::class),
        ];
    }
}
