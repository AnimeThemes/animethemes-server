<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Mutations\Submissions\Wiki;

use App\GraphQL\Schema\Mutations\Submissions\Base\UpdateSubmissionMutation;
use App\GraphQL\Schema\Types\Wiki\AnimeType;
use GraphQL\Type\Definition\ResolveInfo;

class AnimeSubmissionMutation extends UpdateSubmissionMutation
{
    public function __construct()
    {
        parent::__construct('submitUpdateAnime');
    }

    public function description(): string
    {
        return 'Submission information about an anime page.';
    }

    /**
     * The base return type of the mutation.
     */
    public function baseType(): AnimeType
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
