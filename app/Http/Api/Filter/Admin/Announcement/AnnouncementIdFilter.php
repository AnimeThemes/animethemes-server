<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Admin\Announcement;

use App\Http\Api\Filter\IntFilter;
use Illuminate\Support\Collection;
use App\Models\Admin\Announcement;

/**
 * Class AnnouncementIdFilter.
 */
class AnnouncementIdFilter extends IntFilter
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
        return (new Announcement())->getKeyName();
    }
}
