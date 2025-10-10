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
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TrackEntryIdField extends Field implements CreatableField, FilterableField, SelectableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, PlaylistTrack::ATTRIBUTE_ENTRY);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'integer',
            Rule::exists(AnimeThemeEntry::class, AnimeThemeEntry::ATTRIBUTE_ID),
            Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)
                ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $this->resolveVideoId($request)),
        ];
    }

    public function getFilter(): Filter
    {
        return new IntFilter($this->getKey(), $this->getColumn());
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match video relation.
        return true;
    }

    public function getUpdateRules(Request $request): array
    {
        $videoId = $this->resolveVideoId($request);

        return [
            'sometimes',
            'required',
            'integer',
            Rule::exists(AnimeThemeEntry::class, AnimeThemeEntry::ATTRIBUTE_ID),
            Rule::when(
                filled($videoId),
                [
                    Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)
                        ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $videoId),
                ]
            ),
        ];
    }

    /**
     * Get dependent video_id field.
     */
    private function resolveVideoId(Request $request): mixed
    {
        if ($request->has(PlaylistTrack::ATTRIBUTE_VIDEO)) {
            return $request->get(PlaylistTrack::ATTRIBUTE_VIDEO);
        }

        /** @var PlaylistTrack|null $track */
        $track = $request->route('track');

        return $track?->video_id;
    }
}
