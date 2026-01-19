<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Schemas;

use App\GraphQL\Scalars\MixedScalar;
use App\GraphQL\Schema\Mutations\Models\List\Playlist\CreatePlaylistMutation;
use App\GraphQL\Schema\Mutations\Models\List\Playlist\DeletePlaylistMutation;
use App\GraphQL\Schema\Mutations\Models\List\Playlist\Track\CreatePlaylistTrackMutation;
use App\GraphQL\Schema\Mutations\Models\List\Playlist\Track\DeletePlaylistTrackMutation;
use App\GraphQL\Schema\Mutations\Models\List\Playlist\Track\UpdatePlaylistTrackMutation;
use App\GraphQL\Schema\Mutations\Models\List\Playlist\UpdatePlaylistMutation;
use App\GraphQL\Schema\Queries\Admin\CurrentFeaturedThemeQuery;
use App\GraphQL\Schema\Queries\Auth\MeQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Admin\AnnouncementPaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Admin\DumpPaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Admin\FeaturedThemePaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Admin\FeaturePaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Document\PagePaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\List\ExternalProfilePaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\List\Playlist\PlaylistTrackPaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\List\PlaylistPaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\Anime\AnimeSynonymPaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\Anime\AnimeThemePaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\Anime\Theme\AnimeThemeEntryPaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\AnimePaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\ArtistPaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\AudioPaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\ExternalResourcePaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\ImagePaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\SeriesPaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\Song\MembershipPaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\Song\PerformancePaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\SongPaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\StudioPaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\ThemeGroupPaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\Video\VideoScriptPaginationQuery;
use App\GraphQL\Schema\Queries\Models\Pagination\Wiki\VideoPaginationQuery;
use App\GraphQL\Schema\Queries\Models\Singular\Document\PageQuery;
use App\GraphQL\Schema\Queries\Models\Singular\List\Playlist\PlaylistTrackQuery;
use App\GraphQL\Schema\Queries\Models\Singular\List\PlaylistQuery;
use App\GraphQL\Schema\Queries\Models\Singular\Wiki\AnimeQuery;
use App\GraphQL\Schema\Queries\Models\Singular\Wiki\ArtistQuery;
use App\GraphQL\Schema\Queries\Models\Singular\Wiki\SeriesQuery;
use App\GraphQL\Schema\Queries\Models\Singular\Wiki\StudioQuery;
use App\GraphQL\Schema\Queries\Models\Singular\Wiki\VideoQuery;
use App\GraphQL\Schema\Queries\SearchQuery;
use App\GraphQL\Schema\Queries\Wiki\AnimeYearsQuery;
use App\GraphQL\Schema\Queries\Wiki\FindAnimeByExternalSiteQuery;
use App\GraphQL\Schema\Types\Admin\AnnouncementType;
use App\GraphQL\Schema\Types\Admin\DumpType;
use App\GraphQL\Schema\Types\Admin\FeaturedThemeType;
use App\GraphQL\Schema\Types\Admin\FeatureType;
use App\GraphQL\Schema\Types\Auth\PermissionType;
use App\GraphQL\Schema\Types\Auth\RoleType;
use App\GraphQL\Schema\Types\Auth\User\MeType;
use App\GraphQL\Schema\Types\Auth\UserType;
use App\GraphQL\Schema\Types\Document\PageType;
use App\GraphQL\Schema\Types\List\External\ExternalEntryType;
use App\GraphQL\Schema\Types\List\ExternalProfileType;
use App\GraphQL\Schema\Types\List\Playlist\PlaylistTrackType;
use App\GraphQL\Schema\Types\List\PlaylistType;
use App\GraphQL\Schema\Types\MessageResponseType;
use App\GraphQL\Schema\Types\SearchType;
use App\GraphQL\Schema\Types\User\Notification\ExternalProfileSyncedNotificationType;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeSynonymType;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeThemeType;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeYear\AnimeYearSeasonsType;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeYear\AnimeYearSeasonType;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeYearType;
use App\GraphQL\Schema\Types\Wiki\Anime\Theme\AnimeThemeEntryType;
use App\GraphQL\Schema\Types\Wiki\AnimeType;
use App\GraphQL\Schema\Types\Wiki\ArtistType;
use App\GraphQL\Schema\Types\Wiki\AudioType;
use App\GraphQL\Schema\Types\Wiki\ExternalResourceType;
use App\GraphQL\Schema\Types\Wiki\ImageType;
use App\GraphQL\Schema\Types\Wiki\SeriesType;
use App\GraphQL\Schema\Types\Wiki\Song\MembershipType;
use App\GraphQL\Schema\Types\Wiki\Song\PerformanceType;
use App\GraphQL\Schema\Types\Wiki\SongType;
use App\GraphQL\Schema\Types\Wiki\StudioType;
use App\GraphQL\Schema\Types\Wiki\ThemeGroupType;
use App\GraphQL\Schema\Types\Wiki\Video\VideoScriptType;
use App\GraphQL\Schema\Types\Wiki\VideoType;
use App\GraphQL\Schema\Unions\ImageableUnion;
use App\GraphQL\Schema\Unions\LikedUnion;
use App\GraphQL\Schema\Unions\NotificationUnion;
use App\GraphQL\Schema\Unions\PerformanceArtistUnion;
use App\GraphQL\Schema\Unions\ResourceableUnion;
use App\Http\Middleware\GraphQL\LogGraphQLRequest;
use App\Http\Middleware\GraphQL\MaxCount;
use Illuminate\Support\Facades\Config;
use Rebing\GraphQL\GraphQLController;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class V1Schema implements ConfigConvertible
{
    public function toConfig(): array
    {
        return [
            // Also supported array syntax: `[\Rebing\GraphQL\GraphQLController::class, 'query']`
            'controller' => GraphQLController::class.'@query',

            'query' => [
                // Admin
                AnnouncementPaginationQuery::class,
                DumpPaginationQuery::class,
                FeaturePaginationQuery::class,
                FeaturedThemePaginationQuery::class,
                CurrentFeaturedThemeQuery::class,

                // Auth
                MeQuery::class,

                // Document
                PageQuery::class,
                PagePaginationQuery::class,

                // List
                ExternalProfilePaginationQuery::class,
                PlaylistQuery::class,
                PlaylistTrackQuery::class,
                PlaylistPaginationQuery::class,
                PlaylistTrackPaginationQuery::class,

                // Wiki
                AnimeQuery::class,
                AnimePaginationQuery::class,
                AnimeSynonymPaginationQuery::class,
                AnimeThemePaginationQuery::class,
                AnimeThemeEntryPaginationQuery::class,
                ArtistQuery::class,
                ArtistPaginationQuery::class,
                AudioPaginationQuery::class,
                ExternalResourcePaginationQuery::class,
                ImagePaginationQuery::class,
                MembershipPaginationQuery::class,
                PerformancePaginationQuery::class,
                SeriesQuery::class,
                SeriesPaginationQuery::class,
                SongPaginationQuery::class,
                StudioQuery::class,
                StudioPaginationQuery::class,
                ThemeGroupPaginationQuery::class,
                VideoQuery::class,
                VideoPaginationQuery::class,
                VideoScriptPaginationQuery::class,

                // Others
                AnimeYearsQuery::class,
                FindAnimeByExternalSiteQuery::class,
                SearchQuery::class,
            ],

            'mutation' => [
                CreatePlaylistMutation::class,
                UpdatePlaylistMutation::class,
                DeletePlaylistMutation::class,

                CreatePlaylistTrackMutation::class,
                UpdatePlaylistTrackMutation::class,
                DeletePlaylistTrackMutation::class,
            ],

            'types' => [
                // Admin
                AnnouncementType::class,
                DumpType::class,
                FeatureType::class,
                FeaturedThemeType::class,

                // Auth
                MeType::class,
                RoleType::class,
                PermissionType::class,
                UserType::class,

                // Document
                PageType::class,

                // List
                ExternalProfileType::class,
                ExternalEntryType::class,
                PlaylistType::class,
                PlaylistTrackType::class,

                // User
                ExternalProfileSyncedNotificationType::class,

                // Wiki
                AnimeType::class,
                AnimeSynonymType::class,
                AnimeThemeType::class,
                AnimeThemeEntryType::class,
                ArtistType::class,
                AudioType::class,
                ExternalResourceType::class,
                ImageType::class,
                MembershipType::class,
                PerformanceType::class,
                SeriesType::class,
                SongType::class,
                StudioType::class,
                ThemeGroupType::class,
                VideoType::class,
                VideoScriptType::class,

                // Others
                AnimeYearSeasonsType::class,
                AnimeYearSeasonType::class,
                AnimeYearType::class,
                MessageResponseType::class,
                SearchType::class,

                // Pivot
                // ResourceableType::class,

                // Unions

                ImageableUnion::class,
                LikedUnion::class,
                NotificationUnion::class,
                PerformanceArtistUnion::class,
                ResourceableUnion::class,

                // Scalars
                MixedScalar::class,
            ],
            // Laravel HTTP middleware
            'middleware' => [
                // Allow client to get full database.
                MaxCount::class,

                // Logs GraphQL Requests.
                LogGraphQLRequest::class,
            ],
            // Which HTTP methods to support; must be given in UPPERCASE!
            'method' => ['POST'],

            // An array of middlewares, overrides the global ones
            'execution_middleware' => [],

            'route_attributes' => [
                'domain' => Config::get('graphql.domain'),
            ],
        ];
    }
}
