<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Enums\Models\Wiki\AnimeSeason;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\Wiki\Anime;
use Illuminate\Validation\Rules\Enum;

class AnimeSeasonField extends EnumField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_SEASON, AnimeSeason::class);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The premiere season of the anime';
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
            new Enum(AnimeSeason::class),
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
            new Enum(AnimeSeason::class),
        ];
    }
}
