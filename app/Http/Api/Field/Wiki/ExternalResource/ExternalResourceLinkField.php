<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\ExternalResource;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Field\StringField;
use App\Models\Wiki\ExternalResource;
use App\Rules\Wiki\Resource\ResourceLinkFormatRule;
use Illuminate\Http\Request;

/**
 * Class ExternalResourceLinkField.
 */
class ExternalResourceLinkField extends StringField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalResource::ATTRIBUTE_LINK);
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
            'max:192',
            'url',
            new ResourceLinkFormatRule($this->resolveSite($request)),
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
            'max:192',
            'url',
            new ResourceLinkFormatRule($this->resolveSite($request)),
        ];
    }

    /**
     * Resolve site field from request.
     *
     * @param  Request  $request
     * @return ResourceSite|null
     */
    protected function resolveSite(Request $request): ?ResourceSite
    {
        if ($request->has(ExternalResource::ATTRIBUTE_SITE)) {
            $site = $request->input(ExternalResource::ATTRIBUTE_SITE);

            return is_numeric($site) && ResourceSite::hasValue(intval($site))
                ? ResourceSite::fromValue(intval($site))
                : null;
        }

        $resource = $request->route('resource');
        if ($resource instanceof ExternalResource) {
            return $resource->site;
        }

        return null;
    }
}
