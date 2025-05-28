<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Search;

use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\Wiki\StudioType;
use GraphQL\Type\Definition\Type;

/**
 * Class SearchStudiosField.
 */
class SearchStudiosField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct('studios', nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The studio results of the search';
    }

    /**
     * The type returned by the field.
     *
     * @return Type
     */
    protected function type(): Type
    {
        return Type::listOf(Type::nonNull(new StudioType()));
    }
}
