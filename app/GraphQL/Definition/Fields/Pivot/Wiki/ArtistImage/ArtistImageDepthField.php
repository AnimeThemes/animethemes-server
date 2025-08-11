<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistImage;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\IntField;
use App\Pivots\Wiki\ArtistImage;

class ArtistImageDepthField extends IntField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(ArtistImage::ATTRIBUTE_DEPTH);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'Used to sort the artist images';
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
            'sometimes',
            'required',
            'integer',
            'min:1',
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
            'integer',
            'min:1',
        ];
    }
}
