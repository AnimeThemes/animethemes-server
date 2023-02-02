<?php

declare(strict_types=1);

namespace App\Contracts\Http\Api\Field;

use App\Http\Api\Query\ReadQuery;

/**
 * Interface SelectableField.
 */
interface SelectableField
{
    /**
     * Determine if the field should be included in the select clause of our query.
     *
     * @param  ReadQuery  $query
     * @return bool
     */
    public function shouldSelect(ReadQuery $query): bool;
}
