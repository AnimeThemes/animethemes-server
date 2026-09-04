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
use App\Models\Wiki\Entry;
use App\Pivots\Wiki\EntryVideo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeaturedThemeEntryIdField extends Field implements CreatableField, SelectableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, FeaturedTheme::ATTRIBUTE_ENTRY);
    }

    public function getCreationRules(Request $request): array
    {
        $videoId = $this->resolveVideoId($request);

        return [
            'sometimes',
            'required',
            'integer',
            Rule::exists(Entry::class, Entry::ATTRIBUTE_ID),
            Rule::when(
                filled($videoId),
                [
                    Rule::exists(EntryVideo::class, EntryVideo::ATTRIBUTE_ENTRY)
                        ->where(EntryVideo::ATTRIBUTE_VIDEO, $this->resolveVideoId($request)),
                ]
            ),
        ];
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match entry relation.
        return true;
    }

    public function getUpdateRules(Request $request): array
    {
        $videoId = $this->resolveVideoId($request);

        return [
            'sometimes',
            'required',
            'integer',
            Rule::exists(Entry::class, Entry::ATTRIBUTE_ID),
            Rule::when(
                filled($videoId),
                [
                    Rule::exists(EntryVideo::class, EntryVideo::ATTRIBUTE_ENTRY)
                        ->where(EntryVideo::ATTRIBUTE_VIDEO, $this->resolveVideoId($request)),
                ]
            ),
        ];
    }

    /**
     * Get dependent video_id field.
     */
    private function resolveVideoId(Request $request): mixed
    {
        if ($request->has(FeaturedTheme::ATTRIBUTE_VIDEO)) {
            return $request->input(FeaturedTheme::ATTRIBUTE_VIDEO);
        }

        /** @var FeaturedTheme|null $featuredTheme */
        $featuredTheme = $request->route('featuredtheme');

        return $featuredTheme?->video_id;
    }
}
