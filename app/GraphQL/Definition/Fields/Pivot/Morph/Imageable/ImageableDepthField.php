<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Morph\Imageable;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\IntField;
use App\Pivots\Morph\Imageable;

class ImageableDepthField extends IntField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Imageable::ATTRIBUTE_DEPTH);
    }

    public function description(): string
    {
        return 'Used to sort the images';
    }

    /**
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
