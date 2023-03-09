<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\List\PlaylistImage;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\List\Playlist;
use App\Pivots\List\PlaylistImage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class PlaylistImagePlaylistIdField.
 */
class PlaylistImagePlaylistIdField extends Field implements CreatableField, SelectableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, PlaylistImage::ATTRIBUTE_PLAYLIST);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'integer',
            Rule::exists(Playlist::TABLE, Playlist::ATTRIBUTE_ID),
        ];
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
        // Needed to match playlist relation.
        return true;
    }
}
