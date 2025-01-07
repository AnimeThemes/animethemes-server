<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Wiki\ArtistMember;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class ArtistMemberMemberIdField.
 */
class ArtistMemberMemberIdField extends Field implements CreatableField, SelectableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ArtistMember::ATTRIBUTE_MEMBER);
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  Query  $query
     * @param  Schema  $schema
     * @return bool
     */
    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match member relation.
        return true;
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        $artistField = ArtistMember::ATTRIBUTE_ARTIST;

        return [
            'required',
            'integer',
            "different:{$artistField}",
            Rule::exists(Artist::class, Artist::ATTRIBUTE_ID),
            Rule::unique(ArtistMember::class, ArtistMember::ATTRIBUTE_MEMBER)
                ->where(fn (Builder $query) => $query->where(ArtistMember::ATTRIBUTE_ARTIST, $request->get(ArtistMember::ATTRIBUTE_ARTIST))),
        ];
    }
}
