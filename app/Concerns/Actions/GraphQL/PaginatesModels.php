<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait PaginatesModels
{
    public function paginate(Builder $builder, array $args): Paginator
    {
        $first = Arr::get($args, 'first');
        $page = Arr::get($args, 'page');

        return $builder->paginate($first, page: $page);
    }
}
