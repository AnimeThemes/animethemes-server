<?php

declare(strict_types=1);

namespace App\Events\Wiki\Anime\Synonym;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;

/**
 * Class SynonymEvent.
 */
abstract class SynonymEvent
{
    /**
     * The synonym that has fired this event.
     *
     * @var AnimeSynonym
     */
    protected AnimeSynonym $synonym;

    /**
     * The anime that the synonym belongs to.
     *
     * @var Anime
     */
    protected Anime $anime;

    /**
     * Create a new event instance.
     *
     * @param  AnimeSynonym  $synonym
     * @return void
     */
    public function __construct(AnimeSynonym $synonym)
    {
        $this->synonym = $synonym;
        $this->anime = $synonym->anime;
    }

    /**
     * Get the synonym that has fired this event.
     *
     * @return AnimeSynonym
     */
    public function getSynonym(): AnimeSynonym
    {
        return $this->synonym;
    }

    /**
     * Get the anime that the synonym belongs to.
     *
     * @return Anime
     */
    public function getAnime(): Anime
    {
        return $this->anime;
    }
}
