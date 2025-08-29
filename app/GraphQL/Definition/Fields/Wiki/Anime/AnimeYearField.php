<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\IntField;
use App\Models\Wiki\Anime;

class AnimeYearField extends IntField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_YEAR);
    }

    public function description(): string
    {
        return 'The premiere year of the anime';
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        $nextYear = intval(date('Y')) + 1;

        return [
            'required',
            'integer',
            'digits:4',
            'min:1960',
            "max:$nextYear",
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
        $nextYear = intval(date('Y')) + 1;

        return [
            'sometimes',
            'required',
            'integer',
            'digits:4',
            'min:1960',
            "max:$nextYear",
        ];
    }
}
