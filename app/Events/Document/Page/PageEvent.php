<?php

declare(strict_types=1);

namespace App\Events\Document\Page;

use App\Models\Document\Page;

/**
 * Class PageEvent.
 */
abstract class PageEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Page  $page
     * @return void
     */
    public function __construct(protected Page $page)
    {
    }

    /**
     * Get the page that has fired this event.
     *
     * @return Page
     */
    public function getPage(): Page
    {
        return $this->page;
    }
}
