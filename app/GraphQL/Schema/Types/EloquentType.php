<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class EloquentType extends BaseType
{
    public static array $typesToLoad = [];

    public function __construct()
    {
        if (! in_array($this, static::$typesToLoad)) {
            static::$typesToLoad[] = $this;
        }
    }

    /**
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
        return Str::of(static::class)
            ->replace('GraphQL\\Schema\\Types', 'Models')
            ->remove('Type')
            ->__toString();
    }
}
