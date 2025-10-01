<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use Illuminate\Http\Request;

interface UpdatableField
{
    public function getUpdateRules(Request $request): array;
}
