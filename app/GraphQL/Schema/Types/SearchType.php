<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types;

use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Search\SearchAnimeField;
use App\GraphQL\Schema\Fields\Search\SearchAnimeThemesField;
use App\GraphQL\Schema\Fields\Search\SearchArtistsField;
use App\GraphQL\Schema\Fields\Search\SearchPlaylistsField;
use App\GraphQL\Schema\Fields\Search\SearchSeriesField;
use App\GraphQL\Schema\Fields\Search\SearchSongsField;
use App\GraphQL\Schema\Fields\Search\SearchStudiosField;
use App\GraphQL\Schema\Fields\Search\SearchVideosField;

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
