<?php

declare(strict_types=1);

namespace App\Http\Api\Field\List\Playlist\Track;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\FilterableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Filter\Filter;
use App\Http\Api\Filter\IntFilter;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TrackVideoIdField extends Field implements CreatableField, FilterableField, SelectableField, UpdatableField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, PlaylistTrack::ATTRIBUTE_VIDEO);
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
            Rule::exists(Video::class, Video::ATTRIBUTE_ID),
            Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO)
                ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $this->resolveEntryId($request)),
        ];
    }

    /**
     * Get the filter that can be applied to the field.
     *
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return new IntFilter($this->getKey(), $this->getColumn());
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
        // Needed to match video relation.
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
        $entryId = $this->resolveEntryId($request);

        return [
            'sometimes',
            'required',
            'integer',
            Rule::exists(Video::class, Video::ATTRIBUTE_ID),
            Rule::when(
                ! empty($entryId),
                [
                    Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO)
                        ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $this->resolveEntryId($request)),
                ]
            ),
        ];
    }

    /**
     * Get dependent entry_id field.
     *
     * @param  Request  $request
     * @return mixed
     */
    private function resolveEntryId(Request $request): mixed
    {
        if ($request->has(PlaylistTrack::ATTRIBUTE_ENTRY)) {
            return $request->get(PlaylistTrack::ATTRIBUTE_ENTRY);
        }

        /** @var PlaylistTrack|null $track */
        $track = $request->route('track');

        return $track?->entry_id;
    }
}
