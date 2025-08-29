<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\ExternalResource;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Enums\Models\Wiki\ResourceSite;
use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\ExternalResource;
use App\Rules\Wiki\Resource\ResourceLinkFormatRule;
use Illuminate\Support\Arr;

class ExternalResourceLinkField extends StringField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(ExternalResource::ATTRIBUTE_LINK, nullable: false);
    }

    public function description(): string
    {
        return 'The URL of the external site';
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        $site = ResourceSite::from(Arr::get($args, ExternalResource::ATTRIBUTE_SITE));

        return [
            'bail',
            'required',
            'max:192',
            'url',
            new ResourceLinkFormatRule($site),
        ];
    }

    /**
     * Set the update validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getUpdateRules(array $args): array
    {
        $site = ResourceSite::from(Arr::get($args, ExternalResource::ATTRIBUTE_SITE));

        return [
            'bail',
            'sometimes',
            'required',
            'max:192',
            'url',
            new ResourceLinkFormatRule($site),
        ];
    }
}
