<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Mutations\Reports\Wiki;

use App\GraphQL\Definition\Mutations\Reports\Base\UpdateReportMutation;
use App\GraphQL\Definition\Types\Wiki\AnimeType;

class AnimeReportMutation extends UpdateReportMutation
{
    public function __construct()
    {
        parent::__construct('reportUpdateAnime');
    }

    /**
     * The description of the mutation.
     */
    public function description(): string
    {
        return 'Report information about an anime page.';
    }

    /**
     * The base return type of the mutation.
     */
    public function baseType(): AnimeType
    {
        return new AnimeType();
    }
}
