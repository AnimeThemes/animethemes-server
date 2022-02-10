<?php

declare(strict_types=1);

namespace App\Http\Api\Schema;

/**
 * Class EloquentSchema.
 */
abstract class EloquentSchema extends Schema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    abstract public function model(): string;
}
