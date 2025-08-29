<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api;

use App\Http\Api\Schema\Schema;

interface InteractsWithSchema
{
    public function schema(): Schema;
}
