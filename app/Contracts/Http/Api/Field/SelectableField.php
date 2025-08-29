<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;

interface SelectableField
{
    public function shouldSelect(Query $query, Schema $schema): bool;
}
