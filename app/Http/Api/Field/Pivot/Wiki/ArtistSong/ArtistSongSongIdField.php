<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Wiki\ArtistSong;

use App\Contracts\Http\Api\Field\SelectableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Pivots\Wiki\ArtistSong;

class ArtistSongSongIdField extends Field implements SelectableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ArtistSong::ATTRIBUTE_SONG);
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match song relation.
        return true;
    }
}
