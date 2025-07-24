<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api;

use App\Http\Api\Schema\Schema;

interface InteractsWithSchema
{
    /**
     * The name of the disk.
     */
    public function schema(): Schema;
}
