<?php

namespace App\Events\Entry;

use App\Models\Entry;
use Illuminate\Support\Str;

abstract class EntryEvent
{
    /**
     * The entry that has fired this event.
     *
     * @var \App\Models\Entry
     */
    protected $entry;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Entry $entry
     * @return void
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * Get the entry that has fired this event.
     *
     * @return \App\Models\Entry
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * Get readable name for entry.
     *
     * @return string
     */
    public function getName()
    {
        $entry = $this->getEntry();

        return Str::of($entry->anime->name)
            ->append(' ')
            ->append($entry->theme->slug)
            ->append(empty($entry->version) ? '' : " V{$entry->version}")
            ->__toString();
    }
}
