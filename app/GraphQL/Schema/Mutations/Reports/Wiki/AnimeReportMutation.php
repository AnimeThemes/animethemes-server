<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Reports\Wiki;

use App\GraphQL\Schema\Mutations\Reports\Base\UpdateReportMutation;
use App\GraphQL\Schema\Types\Wiki\AnimeType;
use GraphQL\Type\Definition\ResolveInfo;

class AnimeReportMutation extends UpdateReportMutation
{
    public function __construct()
    {
        parent::__construct('reportUpdateAnime');
    }

    public function description(): string
    {
        return 'Report information about an anime page.';
    }

    /**
     * The base return type of the mutation.
     */
    public function baseRebingType(): AnimeType
    {
        return new AnimeType();
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo): mixed
    {
        // TODO
        return null;
    }
}
