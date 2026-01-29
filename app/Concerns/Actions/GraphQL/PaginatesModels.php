<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use App\Rules\GraphQL\Argument\FirstArgumentRule;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

trait PaginatesModels
{
    public function paginate(Builder $builder, array $args): Paginator
    {
        $first = Arr::get($args, 'first');
        $page = Arr::get($args, 'page');

        Validator::make(['first' => $first], [
            'first' => ['required', 'integer', 'min:1', new FirstArgumentRule()],
        ])->validate();

        return $builder->paginate($first, page: $page);
    }
}
