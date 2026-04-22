<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\GraphQL\Filter\Admin\AnnouncementFilterableColumns;
use App\Enums\GraphQL\Filter\Admin\DumpFilterableColumns;
use App\Enums\GraphQL\Filter\Document\PageFilterableColumns;
use App\Enums\GraphQL\Filter\List\ExternalProfileFilterableColumns;
use App\Enums\GraphQL\Filter\List\Playlist\PlaylistTrackFilterableColumns;
use App\Enums\GraphQL\Filter\List\PlaylistFilterableColumns;
use App\Enums\GraphQL\Filter\Wiki\Anime\AnimeThemeFilterableColumns;
use App\Enums\GraphQL\Filter\Wiki\Anime\Theme\AnimeThemeEntryFilterableColumns;
use App\Enums\GraphQL\Filter\Wiki\AnimeFilterableColumns;
use App\Enums\GraphQL\Filter\Wiki\ArtistFilterableColumns;
use App\Enums\GraphQL\Filter\Wiki\AudioFilterableColumns;
use App\Enums\GraphQL\Filter\Wiki\ExternalResourceFilterableColumns;
use App\Enums\GraphQL\Filter\Wiki\ImageFilterableColumns;
use App\Enums\GraphQL\Filter\Wiki\SeriesFilterableColumns;
use App\Enums\GraphQL\Filter\Wiki\Song\PerformanceFilterableColumns;
use App\Enums\GraphQL\Filter\Wiki\SongFilterableColumns;
use App\Enums\GraphQL\Filter\Wiki\StudioFilterableColumns;
use App\Enums\GraphQL\Filter\Wiki\SynonymFilterableColumns;
use App\Enums\GraphQL\Filter\Wiki\ThemeGroupFilterableColumns;
use App\Enums\GraphQL\Filter\Wiki\VideoFilterableColumns;
use App\Enums\GraphQL\Sort\Admin\AnnouncementSort;
use App\Enums\GraphQL\Sort\Admin\DumpSort;
use App\Enums\GraphQL\Sort\Auth\PermissionSort;
use App\Enums\GraphQL\Sort\Auth\RoleSort;
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
use App\Enums\GraphQL\Sort\Wiki\ExternalResourceSort;
use App\Enums\GraphQL\Sort\Wiki\ImageSort;
use App\Enums\GraphQL\Sort\Wiki\SeriesSort;
use App\Enums\GraphQL\Sort\Wiki\Song\PerformanceSort;
use App\Enums\GraphQL\Sort\Wiki\SongSort;
use App\Enums\GraphQL\Sort\Wiki\StudioSort;
use App\Enums\GraphQL\Sort\Wiki\SynonymSort;
use App\Enums\GraphQL\Sort\Wiki\ThemeGroupSort;
use App\Enums\GraphQL\Sort\Wiki\VideoSort;
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
use GraphQL\Type\Definition\PhpEnumType;
use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\Schema\TypeRegistry;

class GraphQLServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bootEnums();
    }

    protected function bootEnums(): void
    {
        $typeRegistry = resolve(TypeRegistry::class);

        // Filter Enums.
        $typeRegistry->register(new PhpEnumType(AnnouncementFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(DumpFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(PageFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(ExternalProfileFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(PlaylistFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(PlaylistTrackFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(AnimeFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(AnimeThemeFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(AnimeThemeEntryFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(ArtistFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(AudioFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(ExternalResourceFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(ImageFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(PerformanceFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(SeriesFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(SongFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(StudioFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(SynonymFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(ThemeGroupFilterableColumns::class));
        $typeRegistry->register(new PhpEnumType(VideoFilterableColumns::class));

        // Sort Enums.
        $typeRegistry->register(new PhpEnumType(AnnouncementSort::class));
        $typeRegistry->register(new PhpEnumType(DumpSort::class));
        $typeRegistry->register(new PhpEnumType(PermissionSort::class));
        $typeRegistry->register(new PhpEnumType(RoleSort::class));
        $typeRegistry->register(new PhpEnumType(PageSort::class));
        $typeRegistry->register(new PhpEnumType(PlaylistSort::class));
        $typeRegistry->register(new PhpEnumType(PlaylistTrackSort::class));
        $typeRegistry->register(new PhpEnumType(AnimeSort::class));
        $typeRegistry->register(new PhpEnumType(AnimeThemeSort::class));
        $typeRegistry->register(new PhpEnumType(AnimeThemeEntrySort::class));
        $typeRegistry->register(new PhpEnumType(ArtistSort::class));
        $typeRegistry->register(new PhpEnumType(AudioSort::class));
        $typeRegistry->register(new PhpEnumType(ExternalResourceSort::class));
        $typeRegistry->register(new PhpEnumType(ImageSort::class));
        $typeRegistry->register(new PhpEnumType(PerformanceSort::class));
        $typeRegistry->register(new PhpEnumType(SeriesSort::class));
        $typeRegistry->register(new PhpEnumType(SongSort::class));
        $typeRegistry->register(new PhpEnumType(StudioSort::class));
        $typeRegistry->register(new PhpEnumType(SynonymSort::class));
        $typeRegistry->register(new PhpEnumType(ThemeGroupSort::class));
        $typeRegistry->register(new PhpEnumType(VideoSort::class));

        // Pivot Sort Enums.
        $typeRegistry->register(new PhpEnumType(ArtistMemberSort::class));
        $typeRegistry->register(new PhpEnumType(ImageableSort::class));

        $typeRegistry->register(new PhpEnumType(ExternalEntryStatus::class));
        $typeRegistry->register(new PhpEnumType(ExternalProfileSite::class));
        $typeRegistry->register(new PhpEnumType(ExternalProfileVisibility::class));
        $typeRegistry->register(new PhpEnumType(PlaylistVisibility::class));
        $typeRegistry->register(new PhpEnumType(NotificationType::class));
        $typeRegistry->register(new PhpEnumType(AnimeMediaFormat::class));
        $typeRegistry->register(new PhpEnumType(AnimeFormat::class));
        $typeRegistry->register(new PhpEnumType(AnimeSeason::class));
        $typeRegistry->register(new PhpEnumType(AnimeSynonymType::class));
        $typeRegistry->register(new PhpEnumType(ImageFacet::class));
        $typeRegistry->register(new PhpEnumType(ResourceSite::class));
        $typeRegistry->register(new PhpEnumType(SynonymType::class));
        $typeRegistry->register(new PhpEnumType(ThemeType::class));
        $typeRegistry->register(new PhpEnumType(VideoOverlap::class));
        $typeRegistry->register(new PhpEnumType(VideoSource::class));
    }
}
