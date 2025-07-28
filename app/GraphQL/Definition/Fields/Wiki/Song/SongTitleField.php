<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Song;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Song;

class SongTitleField extends StringField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Song::ATTRIBUTE_TITLE);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The name of the composition';
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
