<?php

declare(strict_types=1);

namespace App\Actions\GraphQL;

use App\Concerns\Actions\GraphQL\ConstrainsEagerLoads;
use App\Concerns\Actions\GraphQL\FiltersModels;
use App\GraphQL\Schema\Types\BaseType;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ShowAction
{
    use ConstrainsEagerLoads;
    use FiltersModels;

    public function show(Builder $builder, array $args, BaseType $type, ResolveInfo $resolveInfo): Model
    {
        $this->filter($builder, $args, $type);

        $this->constrainEagerLoads($builder, $resolveInfo, $type);

        return $builder->firstOrFail();
    }
}
