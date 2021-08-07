<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Wiki\Theme;

use App\Http\Api\Filter\IntFilter;
use Illuminate\Support\Collection;
use App\Models\Wiki\Theme;

/**
 * Class ThemeIdFilter.
 */
class ThemeIdFilter extends IntFilter
{
    /**
     * Create a new filter instance.
     *
     * @param Collection $criteria
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
        return (new Theme())->getKeyName();
    }
}
