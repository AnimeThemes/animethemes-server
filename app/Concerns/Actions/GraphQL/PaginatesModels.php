<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

trait PaginatesModels
{
    public function paginate(Builder $builder, array $args): Paginator
    {
        $first = Arr::get($args, 'first');
        $page = Arr::get($args, 'page');

        $maxCount = Config::get('graphql.pagination_values.max_count');

        Validator::make(['first' => $first], [
            'first' => [
                'required', 'integer', 'min:1',
                function ($attribute, $value, $fail) use ($maxCount, $first): void {
                    if ($maxCount !== null && $value > $maxCount) {
                        $fail("You may request at most {$maxCount} items. Got {$first}. Fetch in smaller chuncks.");
                    }
                },
            ],
        ])->validate();

        return $builder->paginate($first, page: $page);
    }
}
