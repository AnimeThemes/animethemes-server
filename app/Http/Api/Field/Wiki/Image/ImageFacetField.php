<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Image;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Image;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ImageFacetField extends EnumField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Image::ATTRIBUTE_FACET, ImageFacet::class);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            new Enum(ImageFacet::class),
        ];
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            new Enum(ImageFacet::class),
        ];
    }
}
