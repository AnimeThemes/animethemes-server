<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Image;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Enums\Models\Wiki\ImageFacet;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\Wiki\Image;
use Illuminate\Validation\Rules\Enum;

class ImageFacetField extends EnumField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Image::ATTRIBUTE_FACET, ImageFacet::class, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The component that the resource is intended for';
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            new Enum(ImageFacet::class),
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
        return [
            'sometimes',
            'required',
            new Enum(ImageFacet::class),
        ];
    }
}
