<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime;

use App\GraphQL\Definition\Fields\IntField;
use App\Models\Wiki\Anime;

class AnimeYearField extends IntField
{
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_YEAR);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The premiere year of the anime';
    }
}
