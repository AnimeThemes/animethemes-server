<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Search;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Wiki\AnimeType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class SearchAnimeField extends Field implements DisplayableField
{
    public function __construct()
    {
        parent::__construct('anime', nullable: false);
    }

    public function description(): string
    {
        return 'The anime results of the search';
    }

    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        return Type::listOf(Type::nonNull(GraphQL::type(new AnimeType()->getName())));
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }
}
