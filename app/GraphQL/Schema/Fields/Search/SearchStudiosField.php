<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Search;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\Wiki\StudioType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class SearchStudiosField extends Field implements DisplayableField
{
    public function __construct()
    {
        parent::__construct('studios', nullable: false);
    }

    public function description(): string
    {
        return 'The studio results of the search';
    }

    /**
     * The type returned by the field.
     */
    public function baseType(): Type
    {
        return Type::listOf(Type::nonNull(GraphQL::type(new StudioType()->getName())));
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }
}
