<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Search;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Wiki\SeriesType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class SearchSeriesField extends Field implements DisplayableField
{
    public function __construct()
    {
        parent::__construct('series', nullable: false);
    }

    public function description(): string
    {
        return 'The series results of the search';
    }

    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        return Type::listOf(Type::nonNull(GraphQL::type(new SeriesType()->getName())));
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }
}
