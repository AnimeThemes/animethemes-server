<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Pivot\Wiki;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Pivot\Wiki\ArtistMember\ArtistMemberAliasField;
use App\Http\Api\Field\Pivot\Wiki\ArtistMember\ArtistMemberArtistIdField;
use App\Http\Api\Field\Pivot\Wiki\ArtistMember\ArtistMemberAsField;
use App\Http\Api\Field\Pivot\Wiki\ArtistMember\ArtistMemberDetailsField;
use App\Http\Api\Field\Pivot\Wiki\ArtistMember\ArtistMemberMemberIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistMemberResource;
use App\Pivots\Wiki\ArtistMember;

class ArtistMemberSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return ArtistMemberResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new ArtistSchema(), ArtistMember::RELATION_ARTIST),
            new AllowedInclude(new ArtistSchema(), ArtistMember::RELATION_MEMBER),
        ]);
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(): array
    {
        return [
            new CreatedAtField($this),
            new UpdatedAtField($this),
            new ArtistMemberArtistIdField($this),
            new ArtistMemberMemberIdField($this),
            new ArtistMemberAliasField($this),
            new ArtistMemberAsField($this),
            new ArtistMemberDetailsField($this),
        ];
    }
}
