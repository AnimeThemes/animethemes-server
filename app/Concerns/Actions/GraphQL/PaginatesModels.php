<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use App\Exceptions\GraphQL\ClientValidationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

trait PaginatesModels
{
    public function paginate(Builder $builder, array $args): Paginator
    {
        $first = Arr::get($args, 'first');
        $page = Arr::get($args, 'page');

        $maxCount = Config::get('graphql.pagination_values.max_count');
        if ($maxCount !== null && $first > $maxCount) {
            throw new ClientValidationException("Maximum first value is {$maxCount}. Got {$first}. Fetch in smaller chuncks.");
        }

        return $builder->paginate($first, page: $page);
    }
}
