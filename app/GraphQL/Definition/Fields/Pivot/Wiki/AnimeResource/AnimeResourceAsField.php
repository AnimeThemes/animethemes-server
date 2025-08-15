<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Wiki\AnimeResource;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\StringField;
use App\Pivots\Wiki\AnimeResource;

class AnimeResourceAsField extends StringField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(AnimeResource::ATTRIBUTE_AS);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'Used to distinguish resources that map to the same anime';
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
            'nullable',
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
            'nullable',
            'string',
            'max:192',
        ];
    }
}
