<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki\Song;

use App\Contracts\Http\Api\Schema\SearchableSchema;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Wiki\Song\Membership\MembershipAliasField;
use App\Http\Api\Field\Wiki\Song\Membership\MembershipArtistIdField;
use App\Http\Api\Field\Wiki\Song\Membership\MembershipAsField;
use App\Http\Api\Field\Wiki\Song\Membership\MembershipMemberIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Resources\Wiki\Song\Resource\MembershipResource;
use App\Models\Wiki\Song\Membership;

class MembershipSchema extends EloquentSchema implements SearchableSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return MembershipResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new ArtistSchema(), Membership::RELATION_ARTIST),
            new AllowedInclude(new ArtistSchema(), Membership::RELATION_MEMBER),
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
                new IdField($this, Membership::ATTRIBUTE_ID),
                new MembershipArtistIdField($this),
                new MembershipMemberIdField($this),
                new MembershipAliasField($this),
                new MembershipAsField($this),
            ],
        );
    }
}
