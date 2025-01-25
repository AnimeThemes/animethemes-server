<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Schema;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface SchemaHasDifferentModelPath.
 */
interface SchemaHasDifferentModelPath
{
    /**
     * Get the model of the schema.
     *
     * @return class-string<Model>
     */
    public function model(): string;
}
