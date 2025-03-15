<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Wiki\ArtistImage;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\IntField;
use App\Http\Api\Schema\Schema;
use App\Pivots\Wiki\ArtistImage;
use Illuminate\Http\Request;

/**
 * Class ArtistImageDepthField.
 */
class ArtistImageDepthField extends IntField implements CreatableField, UpdatableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ArtistImage::ATTRIBUTE_DEPTH);
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
            'sometimes',
            'required',
            'integer',
            'min:1',
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
            'integer',
            'min:1',
        ];
    }
}
