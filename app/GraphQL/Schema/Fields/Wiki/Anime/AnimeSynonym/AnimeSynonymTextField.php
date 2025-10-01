<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Anime\AnimeSynonym;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\StringField;
use App\Models\Wiki\Anime\AnimeSynonym;

class AnimeSynonymTextField extends StringField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(AnimeSynonym::ATTRIBUTE_TEXT, nullable: false);
    }

    public function description(): string
    {
        return 'The alternate title or common abbreviations';
    }

    /**
     * @param  array<string, mixed>  $args
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
     * @param  array<string, mixed>  $args
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
