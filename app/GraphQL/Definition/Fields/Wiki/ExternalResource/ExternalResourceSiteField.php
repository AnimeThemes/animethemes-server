<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\ExternalResource;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Enums\Models\Wiki\ResourceSite;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\Wiki\ExternalResource;
use App\Rules\Wiki\Resource\ResourceSiteMatchesLinkRule;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Enum;

class ExternalResourceSiteField extends EnumField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(ExternalResource::ATTRIBUTE_SITE, ResourceSite::class, nullable: false);
    }

    public function description(): string
    {
        return 'The external site that the resource belongs to';
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'bail',
            'required',
            new Enum(ResourceSite::class),
            new ResourceSiteMatchesLinkRule(Arr::get($args, ExternalResource::ATTRIBUTE_LINK)),
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'bail',
            'sometimes',
            'required',
            new Enum(ResourceSite::class),
            new ResourceSiteMatchesLinkRule(Arr::get($args, ExternalResource::ATTRIBUTE_LINK)),
        ];
    }
}
