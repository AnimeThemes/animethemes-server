<?php

declare(strict_types=1);

namespace App\Actions\GraphQL;

use App\Concerns\Actions\GraphQL\ConstrainsEagerLoads;
use App\Concerns\Actions\GraphQL\PaginatesModels;
use App\Concerns\Actions\GraphQL\SearchModels;
use App\Concerns\Actions\GraphQL\SortsModels;
use App\GraphQL\Schema\Types\BaseType;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class IndexAction
{
    use ConstrainsEagerLoads;
    use PaginatesModels;
    use SearchModels;
    use SortsModels;

    public function index(Builder $builder, array $args, BaseType $type, ResolveInfo $resolveInfo): Paginator
    {
        $this->search($builder, $args);

        $this->filter($builder, $args, $type);

        $this->sort($builder, $args, $type);

        $this->constrainEagerLoads($builder, $resolveInfo, $type);

        return $this->paginate($builder, $args);
    }
}
