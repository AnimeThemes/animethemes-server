<?php

namespace App\Events\Synonym;

use App\Models\Synonym;

abstract class SynonymEvent
{
    /**
     * The synonym that has fired this event.
     *
     * @var \App\Models\Synonym
     */
    protected $synonym;

    /**
     * The anime that the synonym belongs to.
     *
     * @var \App\Models\Anime
     */
    protected $anime;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Synonym $synonym
     * @return void
     */
    public function __construct(Synonym $synonym)
    {
        $this->synonym = $synonym;
        $this->anime = $synonym->anime;
    }

    /**
     * Get the synonym that has fired this event.
     *
     * @return \App\Models\Synonym
     */
    public function getSynonym()
    {
        return $this->synonym;
    }

    /**
     * Get the anime that the synonym belongs to
     *
     * @return \App\Models\Anime
     */
    public function getAnime()
    {
        return $this->anime;
    }
}
