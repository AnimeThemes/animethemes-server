<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Base;

use App\GraphQL\Definition\Fields\IntField;

class IdField extends IntField
{
    /**
     * Initializes the IdField with an optional custom column name.
     *
     * @param  string  $column
     */
    public function __construct(protected string $column = 'id')
    {
        parent::__construct($column, 'id', false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The primary key of the resource';
    }
}
