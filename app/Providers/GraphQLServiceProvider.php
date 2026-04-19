<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\GraphQL\Filter\ComparisonOperator;
use App\Enums\GraphQL\Filter\TrashedFilter;
use App\Enums\GraphQL\Sort\Admin\AnnouncementSort;
use App\Enums\GraphQL\Sort\Admin\DumpSort;
use App\Enums\GraphQL\Sort\Document\PageSort;
use App\Enums\GraphQL\Sort\List\Playlist\PlaylistTrackSort;
use App\Enums\GraphQL\Sort\List\PlaylistSort;
use App\Enums\GraphQL\Sort\Pivot\ArtistMemberSort;
use App\Enums\GraphQL\Sort\Pivot\ImageableSort;
use App\Enums\GraphQL\Sort\Wiki\Anime\AnimeTheme\AnimeThemeEntrySort;
use App\Enums\GraphQL\Sort\Wiki\Anime\AnimeThemeSort;
use App\Enums\GraphQL\Sort\Wiki\AnimeSort;
use App\Enums\GraphQL\Sort\Wiki\ArtistSort;
use App\Enums\GraphQL\Sort\Wiki\AudioSort;
use App\Enums\GraphQL\Sort\Wiki\ImageSort;
use App\Enums\GraphQL\Sort\Wiki\SeriesSort;
use App\Enums\GraphQL\Sort\Wiki\Song\PerformanceSort;
use App\Enums\GraphQL\Sort\Wiki\SongSort;
use App\Enums\GraphQL\Sort\Wiki\StudioSort;
use App\Enums\GraphQL\Sort\Wiki\SynonymSort;
use App\Enums\GraphQL\Sort\Wiki\VideoSort;
use App\Enums\GraphQL\SortDirection;
use App\Enums\Models\List\ExternalEntryStatus;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Models\User\NotificationType;
use App\Enums\Models\Wiki\AnimeFormat;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\SynonymType;
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
        // Query Complexity rule on demand
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
        // Sort Enums.
        GraphQL::addType(new EnumType(AnnouncementSort::class));
        GraphQL::addType(new EnumType(DumpSort::class));
        GraphQL::addType(new EnumType(PageSort::class));
        GraphQL::addType(new EnumType(PlaylistSort::class));
        GraphQL::addType(new EnumType(PlaylistTrackSort::class));
        GraphQL::addType(new EnumType(AnimeSort::class));
        GraphQL::addType(new EnumType(AnimeThemeSort::class));
        GraphQL::addType(new EnumType(AnimeThemeEntrySort::class));
        GraphQL::addType(new EnumType(ArtistSort::class));
        GraphQL::addType(new EnumType(AudioSort::class));
        GraphQL::addType(new EnumType(ImageSort::class));
        GraphQL::addType(new EnumType(PerformanceSort::class));
        GraphQL::addType(new EnumType(SeriesSort::class));
        GraphQL::addType(new EnumType(SongSort::class));
        GraphQL::addType(new EnumType(StudioSort::class));
        GraphQL::addType(new EnumType(SynonymSort::class));
        GraphQL::addType(new EnumType(VideoSort::class));

        // Pivot Sort Enums.
        GraphQL::addType(new EnumType(ArtistMemberSort::class));
        GraphQL::addType(new EnumType(ImageableSort::class));

        GraphQL::addType(new EnumType(ComparisonOperator::class));
        GraphQL::addType(new EnumType(TrashedFilter::class));
        GraphQL::addType(new EnumType(SortDirection::class));
        GraphQL::addType(new EnumType(ExternalEntryStatus::class));
        GraphQL::addType(new EnumType(ExternalProfileSite::class));
        GraphQL::addType(new EnumType(ExternalProfileVisibility::class));
        GraphQL::addType(new EnumType(PlaylistVisibility::class));
        GraphQL::addType(new EnumType(NotificationType::class));
        GraphQL::addType(new EnumType(AnimeMediaFormat::class));
        GraphQL::addType(new EnumType(AnimeFormat::class));
        GraphQL::addType(new EnumType(AnimeSeason::class));
        GraphQL::addType(new EnumType(AnimeSynonymType::class));
        GraphQL::addType(new EnumType(ImageFacet::class));
        GraphQL::addType(new EnumType(ResourceSite::class));
        GraphQL::addType(new EnumType(SynonymType::class));
        GraphQL::addType(new EnumType(ThemeType::class));
        GraphQL::addType(new EnumType(VideoOverlap::class));
        GraphQL::addType(new EnumType(VideoSource::class));
    }
}
