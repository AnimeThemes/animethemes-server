<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types;

use App\GraphQL\Definition\Fields\Search\SearchAnimeField;
use App\GraphQL\Definition\Fields\Search\SearchAnimeThemesField;
use App\GraphQL\Definition\Fields\Search\SearchArtistsField;
use App\GraphQL\Definition\Fields\Search\SearchPlaylistsField;
use App\GraphQL\Definition\Fields\Search\SearchSeriesField;
use App\GraphQL\Definition\Fields\Search\SearchSongsField;
use App\GraphQL\Definition\Fields\Search\SearchStudiosField;
use App\GraphQL\Definition\Fields\Search\SearchVideosField;
use App\GraphQL\Definition\Types\BaseType;

/**
 * Class SearchType.
 */
class SearchType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Returns a listing of resources that match a given search term.";
    }

    /**
     * The fields of the type.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            new SearchAnimeField(),
            new SearchArtistsField(),
            new SearchAnimeThemesField(),
            new SearchPlaylistsField(),
            new SearchSeriesField(),
            new SearchSongsField(),
            new SearchStudiosField(),
            new SearchVideosField(),
        ];
    }
}
