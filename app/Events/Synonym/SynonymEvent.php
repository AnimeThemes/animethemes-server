<?php

declare(strict_types=1);

namespace App\Events\Synonym;

use App\Models\Anime;
use App\Models\Synonym;

/**
 * Class SynonymEvent
 * @package App\Events\Synonym
 */
abstract class SynonymEvent
{
    /**
     * The synonym that has fired this event.
     *
     * @var Synonym
     */
    protected Synonym $synonym;

    /**
     * The anime that the synonym belongs to.
     *
     * @var Anime
     */
    protected Anime $anime;

    /**
     * Create a new event instance.
     *
     * @param Synonym $synonym
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
     * @return Synonym
     */
    public function getSynonym(): Synonym
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
