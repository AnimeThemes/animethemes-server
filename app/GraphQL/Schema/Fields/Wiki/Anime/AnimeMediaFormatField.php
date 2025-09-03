<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Anime;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\GraphQL\Schema\Fields\EnumField;
use App\Models\Wiki\Anime;
use Illuminate\Validation\Rules\Enum;

class AnimeMediaFormatField extends EnumField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_MEDIA_FORMAT, AnimeMediaFormat::class);
    }

    public function description(): string
    {
        return 'The media format of the anime';
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            new Enum(AnimeMediaFormat::class),
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
            new Enum(AnimeMediaFormat::class),
        ];
    }
}
