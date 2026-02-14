<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\User\WatchHistory;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\GraphQL\Schema\Fields\Field;
use App\Models\User\WatchHistory;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class WatchHistoryVideoIdField extends Field implements CreatableField, RequiredOnCreation
{
    public function __construct()
    {
        parent::__construct(WatchHistory::ATTRIBUTE_VIDEO, nullable: false);
    }

    public function description(): string
    {
        return 'The video id';
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
            Rule::exists(Video::class, Video::ATTRIBUTE_ID),
            Rule::exists(AnimeThemeEntryVideo::class, AnimeThemeEntryVideo::ATTRIBUTE_VIDEO)
                ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, Arr::get($args, WatchHistory::ATTRIBUTE_ENTRY)),
        ];
    }
}
