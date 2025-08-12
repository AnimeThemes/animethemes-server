<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

use Illuminate\Database\Eloquent\Model;

interface BindableField
{
    /**
     * The resolver to cast the model.
     */
    public function bindResolver(array $args): ?Model;
}
