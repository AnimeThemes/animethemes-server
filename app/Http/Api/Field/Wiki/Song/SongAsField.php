<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Song;

use App\Http\Api\Field\Field;
use App\Pivots\Wiki\ArtistSong;

/**
 * Class SongAsField.
 */
class SongAsField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ArtistSong::ATTRIBUTE_AS);
    }
}
