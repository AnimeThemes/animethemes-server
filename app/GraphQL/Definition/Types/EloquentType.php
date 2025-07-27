<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class EloquentType extends BaseType
{
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

    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [
            ...parent::directives(),

            'model' => [
                'class' => $this->model(),
            ],
        ];
    }
}
