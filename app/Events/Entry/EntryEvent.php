<?php

declare(strict_types=1);

namespace App\Events\Entry;

use App\Models\Entry;

/**
 * Class EntryEvent
 * @package App\Events\Entry
 */
abstract class EntryEvent
{
    /**
     * The entry that has fired this event.
     *
     * @var Entry
     */
    protected Entry $entry;

    /**
     * Create a new event instance.
     *
     * @param Entry $entry
     * @return void
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * Get the entry that has fired this event.
     *
     * @return Entry
     */
    public function getEntry(): Entry
    {
        return $this->entry;
    }
}
