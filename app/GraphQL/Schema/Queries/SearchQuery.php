<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Queries;

use App\Concerns\Actions\GraphQL\ConstrainsEagerLoads;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\List\PlaylistType;
use App\GraphQL\Schema\Types\SearchType;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeThemeType;
use App\GraphQL\Schema\Types\Wiki\AnimeType;
use App\GraphQL\Schema\Types\Wiki\ArtistType;
use App\GraphQL\Schema\Types\Wiki\SeriesType;
use App\GraphQL\Schema\Types\Wiki\SongType;
use App\GraphQL\Schema\Types\Wiki\StudioType;
use App\GraphQL\Schema\Types\Wiki\VideoType;
use App\GraphQL\Support\Argument\Argument;
use App\GraphQL\Support\Argument\FirstArgument;
use App\GraphQL\Support\Argument\PageArgument;
use App\Models\List\Playlist;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Video;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Number;

class SearchQuery extends BaseQuery
{
    use ConstrainsEagerLoads;

    public function __construct()
    {
        parent::__construct('search', false, false);
    }

    public function description(): string
    {
        return 'Returns a listing of resources that match a given search term.';
    }

    /**
     * The arguments of the type.
     *
     * @return Argument[]
     */
    public function arguments(): array
    {
        return [
            new Argument('search', Type::string())
                ->required(),

            new FirstArgument(),
            new PageArgument(),
        ];
    }

    /**
     * The base return type of the query.
     */
    public function baseType(): SearchType
    {
        return new SearchType();
    }

    /**
     * @return array<string, array>
     */
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo)
    {
        $result = [];
        $fields = $resolveInfo->getFieldSelection();

        if (Arr::get($fields, 'anime')) {
            $result['anime'] = $this->search(Anime::class, $args, $resolveInfo, new AnimeType, 'anime');
        }

        if (Arr::get($fields, 'artists')) {
            $result['artists'] = $this->search(Artist::class, $args, $resolveInfo, new ArtistType, 'artists');
        }

        if (Arr::get($fields, 'animethemes')) {
            $result['animethemes'] = $this->search(AnimeTheme::class, $args, $resolveInfo, new AnimeThemeType, 'animethemes');
        }

        if (Arr::get($fields, 'playlists')) {
            $result['playlists'] = $this->search(Playlist::class, $args, $resolveInfo, new PlaylistType, 'playlists');
        }

        if (Arr::get($fields, 'series')) {
            $result['series'] = $this->search(Series::class, $args, $resolveInfo, new SeriesType, 'series');
        }

        if (Arr::get($fields, 'songs')) {
            $result['songs'] = $this->search(Song::class, $args, $resolveInfo, new SongType, 'songs');
        }

        if (Arr::get($fields, 'studios')) {
            $result['studios'] = $this->search(Studio::class, $args, $resolveInfo, new StudioType, 'studios');
        }

        if (Arr::get($fields, 'videos')) {
            $result['videos'] = $this->search(Video::class, $args, $resolveInfo, new VideoType, 'videos');
        }

        return $result;
    }

    /**
     * @param  class-string<Model>  $model
     */
    protected function search(string $model, array $args, ResolveInfo $resolveInfo, EloquentType $type, string $field): array
    {
        $term = Arr::get($args, 'search');
        $page = Arr::get($args, 'page');
        $first = Number::clamp(Arr::get($args, 'first'), 1, 15);

        $fields = Arr::get($resolveInfo->getFieldSelectionWithAliases(100), "{$field}.{$field}.selectionSet");

        /** @phpstan-ignore-next-line */
        return $model::search($term)
            ->query(fn (Builder $builder) => $this->constrainEagerLoads($builder, $fields, $type))
            ->paginate($first, $page)
            ->items();
    }
}
