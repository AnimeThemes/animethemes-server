<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\Wiki\Anime;

class AnimeMediaFormatField extends EnumField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_MEDIA_FORMAT, AnimeMediaFormat::class);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The media format of the anime';
    }
}
