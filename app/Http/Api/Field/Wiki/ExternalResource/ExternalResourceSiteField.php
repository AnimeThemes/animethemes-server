<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\ExternalResource;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Field\EnumField;
use App\Models\Wiki\ExternalResource;
use App\Rules\Wiki\Resource\ResourceSiteMatchesLinkRule;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;

/**
 * Class ExternalResourceSiteField.
 */
class ExternalResourceSiteField extends EnumField implements CreatableField, UpdatableField
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
            'bail',
            'required',
            new EnumValue(ResourceSite::class),
            new ResourceSiteMatchesLinkRule($this->resolveLink($request)),
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
            'bail',
            'sometimes',
            'required',
            new EnumValue(ResourceSite::class),
            new ResourceSiteMatchesLinkRule($this->resolveLink($request)),
        ];
    }

    /**
     * Resolve link field from request.
     *
     * @param  Request  $request
     * @return string
     */
    protected function resolveLink(Request $request): string
    {
        if ($request->has(ExternalResource::ATTRIBUTE_LINK)) {
            $link = $request->input(ExternalResource::ATTRIBUTE_LINK);

            return is_string($link) ? $link : '';
        }

        $resource = $request->route('resource');
        if ($resource instanceof ExternalResource) {
            return $resource->link;
        }

        return '';
    }
}
