<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Criteria\Field\Criteria;
use App\Http\Api\Field\Field;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Class PlaylistLastIdField.
 */
class PlaylistLastIdField extends Field implements SelectableField, UpdatableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Playlist::ATTRIBUTE_LAST);
    }

    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  Criteria|null  $criteria
     * @return bool
     */
    public function shouldSelect(?Criteria $criteria): bool
    {
        // Needed to match last track relation.
        return true;
    }

    /**
     * Set the update validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        /** @var Playlist|null $playlist */
        $playlist = $request->route('playlist');

        return [
            'sometimes',
            'required',
            'integer',
            Rule::exists(PlaylistTrack::TABLE, PlaylistTrack::ATTRIBUTE_ID)
                ->where(PlaylistTrack::ATTRIBUTE_PLAYLIST, $playlist?->getKey()),
        ];
    }
}
