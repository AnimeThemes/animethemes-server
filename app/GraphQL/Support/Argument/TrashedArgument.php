<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Argument;

use App\Enums\GraphQL\TrashedFilter;
use Rebing\GraphQL\Support\Facades\GraphQL;

class TrashedArgument extends Argument
{
    final public const ARGUMENT = 'trashed';

    public function __construct()
    {
        parent::__construct(self::ARGUMENT, GraphQL::type(class_basename(TrashedFilter::class)));

        $this->withDefaultValue(TrashedFilter::WITHOUT);
    }
}
