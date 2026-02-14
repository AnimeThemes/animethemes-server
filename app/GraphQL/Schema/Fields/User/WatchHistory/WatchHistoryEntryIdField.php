<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\User\WatchHistory;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\GraphQL\Resolvers\User\WatchResolver;
use App\GraphQL\Schema\Fields\Field;
use App\Models\User\WatchHistory;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class WatchHistoryEntryIdField extends Field implements CreatableField, RequiredOnCreation
{
    public function __construct()
    {
        parent::__construct(WatchHistory::ATTRIBUTE_ENTRY, nullable: false);
    }

    public function description(): string
    {
        return 'The entry id';
    }

    public function baseType(): Type
    {
        return Type::int();
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            'integer',
            Rule::exists(AnimeThemeEntry::class, AnimeThemeEntry::ATTRIBUTE_ID),
            Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_ENTRY)
                ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, Arr::get($args, WatchResolver::ATTRIBUTE_VIDEO)),
        ];
    }
}
