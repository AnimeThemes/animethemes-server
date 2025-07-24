<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Wiki\Anime;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AnimeYear
{
    /**
     * Return a value for the field.
     *
     * @param  array  $args  The field arguments passed by the client.
     */
    public function years(null $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): mixed
    {
        return Anime::query()
            ->distinct(Anime::ATTRIBUTE_YEAR)
            ->orderBy(Anime::ATTRIBUTE_YEAR)
            ->pluck(Anime::ATTRIBUTE_YEAR);
    }

    /**
     * Return a value for the field.
     *
     * @param  array  $args  The field arguments passed by the client.
     */
    public function year(null $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): mixed
    {
        $year = Arr::get($args, 'year');

        return Anime::query()
            ->where(Anime::ATTRIBUTE_YEAR, $year)
            ->orderBy(Anime::ATTRIBUTE_NAME)
            ->get()
            ->groupBy(fn (Anime $anime) => Str::lower($anime->season->localize()));
    }
}
