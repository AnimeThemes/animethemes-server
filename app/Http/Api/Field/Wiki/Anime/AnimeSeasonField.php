<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Anime;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class AnimeSeasonField extends EnumField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Anime::ATTRIBUTE_SEASON, AnimeSeason::class);
    }

    /**
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            new Enum(AnimeSeason::class),
        ];
    }

    /**
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            new Enum(AnimeSeason::class),
        ];
    }
}
