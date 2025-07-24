<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Search;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Wiki\StudioType;
use GraphQL\Type\Definition\Type;

class SearchStudiosField extends Field implements DisplayableField
{
    public function __construct()
    {
        parent::__construct('studios', nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The studio results of the search';
    }

    /**
     * The type returned by the field.
     */
    public function type(): Type
    {
        return Type::listOf(Type::nonNull(new StudioType()));
    }

    /**
     * Determine if the field should be displayed to the user.
     */
    public function canBeDisplayed(): bool
    {
        return true;
    }
}
