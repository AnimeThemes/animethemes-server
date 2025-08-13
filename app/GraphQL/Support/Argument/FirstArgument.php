<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Argument;

use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Config;

class FirstArgument extends Argument
{
    public function __construct(bool $isRelation = false)
    {
        parent::__construct('first', Type::int());

        $this->required();

        // Default count set to unlimited for relations for everyone
        // and set to config value for paginator queries.
        $this->withDefaultValue(
            $isRelation
            ? Config::get('graphql.pagination_values.relation.default_count')
            : Config::get('graphql.pagination_values.default_count')
        );
    }
}
