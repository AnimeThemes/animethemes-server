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
use App\Http\Resources\Wiki\Resource\GroupJsonResource;
use App\Models\Wiki\Group;

class GroupSchema extends EloquentSchema implements SearchableSchema
{
    public function type(): string
    {
        return GroupJsonResource::$wrap;
    }

    /**
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
