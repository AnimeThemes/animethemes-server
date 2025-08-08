<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Base;

use App\GraphQL\Definition\Fields\IntField;

class IdUnbindableField extends IntField
{
    public function __construct(protected string $column)
    {
        parent::__construct($column, 'id', false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The primary key of the resource';
    }
}
