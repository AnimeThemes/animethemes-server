<?php

declare(strict_types=1);

namespace App\Search\Builders;

use App\Contracts\Search\SearchBuilder;
use App\Models\List\Playlist;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Video;
use App\Scout\Elasticsearch\Api\Query\ElasticQuery;
use App\Scout\Elasticsearch\Api\Query\List\PlaylistQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\Anime\SynonymQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\Anime\Theme\EntryQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\Anime\ThemeQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\AnimeQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\ArtistQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\SeriesQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\SongQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\StudioQuery;
use App\Scout\Elasticsearch\Api\Query\Wiki\VideoQuery;
use App\Search\Criteria;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Paginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use RuntimeException;

class ElasticSearchBuilder implements SearchBuilder
{
    protected SearchParametersBuilder $builder;

    protected int $perPage = 15;
    protected int $page = 1;

    protected function __construct(protected Model $model) {}

    public static function search(Model $model, Criteria $criteria): SearchBuilder
    {
        $builder = new self($model);

        $builder->builder = static::elasticQuery($model::class)
            ->build($criteria);

        return $builder;
    }

    public function withPagination(int $perPage, int $page = 1): static
    {
        $this->perPage = $perPage;
        $this->page = $page;

        return $this;
    }

    public function toEloquentBuilder(): EloquentBuilder
    {
        return $this->model::query()
            ->whereIn(
                $this->model->getKeyName(),
                $this->keys()
            );
    }

    /**
     * Get the underlying search parameters builder.
     */
    public function getBuilder(): SearchParametersBuilder
    {
        return $this->builder;
    }

    /**
     * @param  class-string<Model>  $model
     */
    public static function elasticQuery(string $model): ElasticQuery
    {
        return match ($model) {
            Playlist::class => new PlaylistQuery(),
            AnimeThemeEntry::class => new EntryQuery(),
            AnimeSynonym::class => new SynonymQuery(),
            AnimeTheme::class => new ThemeQuery(),
            Anime::class => new AnimeQuery(),
            Artist::class => new ArtistQuery(),
            Series::class => new SeriesQuery(),
            Song::class => new SongQuery(),
            Studio::class => new StudioQuery(),
            Video::class => new VideoQuery(),
            default => throw new RuntimeException("No ElasticQuery defined for model: {$model}"),
        };
    }

    /**
     * The keys of the retrieved models.
     *
     * @return int[]
     */
    public function keys(): array
    {
        return Arr::pluck($this->paginate()->items(), $this->model->getKeyName());
    }

    protected function paginate(): Paginator
    {
        return $this->builder->paginate($this->perPage, page: $this->page)->onlyModels();
    }
}
