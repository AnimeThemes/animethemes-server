<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Anime;

class AnimeNameField extends StringField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_NAME, nullable: false);
    }

    public function description(): string
    {
        return 'The primary title of the anime';
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
            'string',
            'max:192',
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
            'string',
            'max:192',
        ];
    }
}
