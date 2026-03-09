<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Inputs;

use Rebing\GraphQL\Support\InputType;

abstract class Input extends InputType
{
    public function __construct()
    {
        $this->attributes['name'] = $this->name();
    }

    public function name(): string
    {
        return class_basename($this);
    }
}
