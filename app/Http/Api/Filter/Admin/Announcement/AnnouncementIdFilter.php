<?php

declare(strict_types=1);

namespace App\Http\Api\Filter\Admin\Announcement;

use App\Http\Api\Filter\IntFilter;
use App\Http\Api\QueryParser;
use App\Models\Admin\Announcement;

/**
 * Class AnnouncementIdFilter.
 */
class AnnouncementIdFilter extends IntFilter
{
    /**
     * Create a new filter instance.
     *
     * @param QueryParser $parser
     */
    public function __construct(QueryParser $parser)
    {
        parent::__construct($parser, 'id');
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
