<?php

declare(strict_types=1);

namespace App\GraphQL\Builders;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

class FindAnimeByExternalSiteBuilder
{
    /**
     * @param  array<string, mixed>  $args
     */
    public function __invoke(Builder $builder, null $_, null $root, $args): Builder
    {
        Validator::make($args, [
            'site' => ['required', new Enum(ResourceSite::class)],
            'id' => ['required_without:link', 'max:100'],
            'link' => ['required_without:id'],
        ])->validate();

        $site = Arr::get($args, 'site');
        $externalId = Arr::get($args, 'id');
        $link = Arr::get($args, 'link');

        $builder->whereRelation(Anime::RELATION_RESOURCES, function (Builder $query) use ($site, $externalId, $link): void {
            $query->where(ExternalResource::ATTRIBUTE_SITE, $site->value);

            if (is_array($externalId)) {
                $query->whereIn(ExternalResource::ATTRIBUTE_EXTERNAL_ID, $externalId);
            }

            if (is_string($link)) {
                $query->where(ExternalResource::ATTRIBUTE_LINK, $link);
            }
        });

        return $builder;
    }
}
