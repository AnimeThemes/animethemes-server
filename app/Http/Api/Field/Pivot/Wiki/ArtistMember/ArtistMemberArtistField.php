<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Wiki\ArtistMember;

use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class ArtistMemberArtistField.
 */
class ArtistMemberArtistField extends Field
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ArtistMember::RELATION_ARTIST);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        $memberField = ArtistMember::RELATION_MEMBER;

        return [
            'required',
            "different:{$memberField}",
            Rule::exists(Artist::class),
            Rule::unique(ArtistMember::class, ArtistMember::ATTRIBUTE_ARTIST)
                ->where(fn (Builder $query) => $query->where(ArtistMember::ATTRIBUTE_MEMBER, $request->get(ArtistMember::ATTRIBUTE_MEMBER))),
        ];
    }
}
