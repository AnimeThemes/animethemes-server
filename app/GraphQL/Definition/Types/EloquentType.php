<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types;

use App\GraphQL\Support\SortableColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;

abstract class EloquentType extends BaseType
{
    public function __construct(bool $first = true)
    {
        if ($first) {
            GraphQL::addType(new SortableColumns($this));
        }
    }

    /**
     * Get the attributes of the type.
     *
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            ...parent::attributes(),

            'model' => $this->model(),
        ];
    }

    /**
     * Get the model string representation for the type.
     *
     * @return class-string<Model>
     */
    public function model(): string
    {
        return Str::of(get_class($this))
            ->replace('GraphQL\\Definition\\Types', 'Models')
            ->remove('Type')
            ->__toString();
    }
}
