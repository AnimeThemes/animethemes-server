<?php

declare(strict_types=1);

namespace App\Enums\Models\Wiki;

use App\Concerns\Enums\LocalizesName;
use Filament\Support\Contracts\HasLabel;
use GraphQL\Type\Definition\Deprecated as GraphQLDeprecated;

/**
 * @deprecated Use SynonymType instead.
 */
#[GraphQLDeprecated('Use SynonymType instead.')]
enum AnimeSynonymType: int implements HasLabel
{
    use LocalizesName;

    case OTHER = 0;
    case NATIVE = 1;
    case ENGLISH = 2;
    case SHORT = 3;
}
