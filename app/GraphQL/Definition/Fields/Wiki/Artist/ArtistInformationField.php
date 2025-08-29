<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Artist;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Artist;

class ArtistInformationField extends StringField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Artist::ATTRIBUTE_INFORMATION);
    }

    public function description(): string
    {
        return 'The brief information of the resource';
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'nullable',
            'string',
            'max:65535',
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'nullable',
            'string',
            'max:65535',
        ];
    }
}
