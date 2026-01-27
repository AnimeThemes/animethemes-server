<?php

declare(strict_types=1);

namespace App\Enums\GraphQL\Filter;

use GraphQL\Type\Definition\Description;

enum TrashedFilter
{
    #[Description('Include soft-deleted models in the result set')]
    case WITH;

    #[Description('Exclude soft-deleted models; only return active models')]
    case WITHOUT;

    #[Description('Return only soft-deleted (trashed) models')]
    case ONLY;
}
