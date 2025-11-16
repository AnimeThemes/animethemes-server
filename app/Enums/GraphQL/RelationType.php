<?php

declare(strict_types=1);

namespace App\Enums\GraphQL;

enum RelationType
{
    case BELONGS_TO;
    case BELONGS_TO_MANY;
    case HAS_MANY;
    case HAS_ONE;
    case MORPH_MANY;
    case MORPH_TO;
}
