<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Song;

use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;
use App\Pivots\Wiki\ArtistSong;

/**
 * Class SongAsField.
 */
class SongAsField extends Field
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ArtistSong::ATTRIBUTE_AS);
    }
}
