<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Search\SearchAnimeField;
use App\GraphQL\Definition\Fields\Search\SearchAnimeThemesField;
use App\GraphQL\Definition\Fields\Search\SearchArtistsField;
use App\GraphQL\Definition\Fields\Search\SearchPlaylistsField;
use App\GraphQL\Definition\Fields\Search\SearchSeriesField;
use App\GraphQL\Definition\Fields\Search\SearchSongsField;
use App\GraphQL\Definition\Fields\Search\SearchStudiosField;
use App\GraphQL\Definition\Fields\Search\SearchVideosField;

class SearchType extends BaseType
{
    public function description(): string
    {
        return 'Returns a listing of resources that match a given search term.';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
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
