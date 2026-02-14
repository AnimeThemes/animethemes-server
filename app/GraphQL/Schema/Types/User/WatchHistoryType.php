<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\User;

use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Relations\BelongsToRelation;
use App\GraphQL\Schema\Fields\User\WatchHistory\WatchHistoryEntryIdField;
use App\GraphQL\Schema\Fields\User\WatchHistory\WatchHistoryVideoIdField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use App\GraphQL\Schema\Types\Wiki\VideoType;
use App\Models\User\WatchHistory;

class WatchHistoryType extends EloquentType
{
    public function description(): string
    {
        return 'Represents the watch history of the authenticated user.';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new WatchHistoryEntryIdField(),
            new WatchHistoryVideoIdField(),
            new BelongsToRelation(new AnimeThemeEntryType(), WatchHistory::RELATION_ENTRY)
                ->nonNullable(),
            new BelongsToRelation(new VideoType(), WatchHistory::RELATION_VIDEO)
                ->nonNullable(),
        ];
    }
}
