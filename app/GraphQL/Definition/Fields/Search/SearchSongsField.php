<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Search;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Wiki\SongType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class SearchSongsField extends Field implements DisplayableField
{
    public function __construct()
    {
        parent::__construct('songs', nullable: false);
    }

    public function description(): string
    {
        return 'The song results of the search';
    }

    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        return Type::listOf(Type::nonNull(GraphQL::type(new SongType()->getName())));
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }
}
