<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Search;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\Wiki\ArtistType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class SearchArtistsField extends Field implements DisplayableField
{
    public function __construct()
    {
        parent::__construct('artists', nullable: false);
    }

    public function description(): string
    {
        return 'The artist results of the search';
    }

    public function baseType(): Type
    {
        return Type::listOf(Type::nonNull(GraphQL::type(new ArtistType()->getName())));
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }
}
