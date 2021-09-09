<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\ExternalResource;

use App\Http\Api\Filter\IntFilter;
use App\Models\Wiki\ExternalResource;
use Illuminate\Support\Collection;

/**
 * Class ExternalResourceIdFilter.
 */
class ExternalResourceIdFilter extends IntFilter
{
    /**
     * Create a new filter instance.
     *
     * @param  Collection  $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'id');
    }

    /**
     * Get filter column.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getColumn(): string
    {
        return (new ExternalResource())->getKeyName();
    }
}
