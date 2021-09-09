<?php

declare(strict_types=1);

namespace App\Http\Api\Sort\Admin\Announcement;

use App\Http\Api\Sort\Sort;
use App\Models\Admin\Announcement;
use Illuminate\Support\Collection;

/**
 * Class AnnouncementIdSort.
 */
class AnnouncementIdSort extends Sort
{
    /**
     * Create a new sort instance.
     *
     * @param  Collection  $criteria
     */
    public function __construct(Collection $criteria)
    {
        parent::__construct($criteria, 'id');
    }

    /**
     * Get sort column.
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
