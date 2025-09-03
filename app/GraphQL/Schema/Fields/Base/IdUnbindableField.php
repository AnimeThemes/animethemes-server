<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Base;

use App\GraphQL\Schema\Fields\IntField;

class IdUnbindableField extends IntField
{
    public function __construct(protected string $column)
    {
        parent::__construct($column, 'id', false);
    }

    public function description(): string
    {
        return 'The primary key of the resource';
    }
}
