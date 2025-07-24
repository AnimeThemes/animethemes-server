<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Group\GroupNameField;
use App\Http\Api\Field\Wiki\Group\GroupSlugField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\Wiki\Resource\GroupResource;
use App\Models\Wiki\Group;

class GroupSchema extends EloquentSchema implements SearchableSchema
{
    /**
     * Get the type of the resource.
     */
    public function type(): string
    {
        return GroupResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), Group::RELATION_ANIME),
            new AllowedInclude(new ThemeSchema(), Group::RELATION_THEMES),
            new AllowedInclude(new VideoSchema(), Group::RELATION_VIDEOS),
        ]);
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IdField($this, Group::ATTRIBUTE_ID),
                new GroupNameField($this),
                new GroupSlugField($this),
            ],
        );
    }
}
