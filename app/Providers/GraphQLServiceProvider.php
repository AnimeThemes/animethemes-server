<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\GraphQL\SortDirection;
use App\Enums\GraphQL\TrashedFilter;
use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Models\User\NotificationType;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\GraphQL\Schema\Enums\EnumType;
use GraphQL\Validator\DocumentValidator;
use GraphQL\Validator\Rules\QueryComplexity as BaseQueryComplexity;
use Illuminate\Support\ServiceProvider;
use Rebing\GraphQL\Support\Facades\GraphQL;

class GraphQLServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        # Query Complexity rule on demand
        DocumentValidator::addRule(
            new class(250) extends BaseQueryComplexity
            {
                protected function isEnabled(): bool
                {
                    return request()->ip() !== '127.0.0.1';
                }
            }
        );

        $this->bootEnums();
    }

    protected function bootEnums(): void
    {
        GraphQL::addType(new EnumType(TrashedFilter::class));
        GraphQL::addType(new EnumType(SortDirection::class));
        GraphQL::addType(new EnumType(ExternalEntryWatchStatus::class));
        GraphQL::addType(new EnumType(ExternalProfileSite::class));
        GraphQL::addType(new EnumType(ExternalProfileVisibility::class));
        GraphQL::addType(new EnumType(PlaylistVisibility::class));
        GraphQL::addType(new EnumType(NotificationType::class));
        GraphQL::addType(new EnumType(AnimeMediaFormat::class));
        GraphQL::addType(new EnumType(AnimeSeason::class));
        GraphQL::addType(new EnumType(AnimeSynonymType::class));
        GraphQL::addType(new EnumType(ImageFacet::class));
        GraphQL::addType(new EnumType(ResourceSite::class));
        GraphQL::addType(new EnumType(ThemeType::class));
        GraphQL::addType(new EnumType(VideoOverlap::class));
        GraphQL::addType(new EnumType(VideoSource::class));
    }
}
