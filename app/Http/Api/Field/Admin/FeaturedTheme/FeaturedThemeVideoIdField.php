<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Admin\FeaturedTheme;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Admin\FeaturedTheme;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeaturedThemeVideoIdField extends Field implements CreatableField, SelectableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, FeaturedTheme::ATTRIBUTE_VIDEO);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @return array
     */
    public function getCreationRules(Request $request): array
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
     * Determine if the field should be included in the select clause of our query.
     */
    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match video relation.
        return true;
    }

    /**
     * Set the update validation rules for the field.
     *
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
     */
    private function resolveEntryId(Request $request): mixed
    {
        if ($request->has(FeaturedTheme::ATTRIBUTE_ENTRY)) {
            return $request->get(FeaturedTheme::ATTRIBUTE_ENTRY);
        }

        /** @var FeaturedTheme|null $featuredTheme */
        $featuredTheme = $request->route('featuredtheme');

        return $featuredTheme?->entry_id;
    }
}
