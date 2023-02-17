<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api;

use App\Http\Api\Schema\Schema;

/**
 * Class InteractsWithSchema.
 */
interface InteractsWithSchema
{
    /**
     * The name of the disk.
     *
     * @return Schema
     */
    public function schema(): Schema;
}
