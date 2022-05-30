<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Song;

use App\Models\Wiki\Song;
use App\Scout\Elasticsearch\Api\Field\StringField;

/**
 * Class SongTitleField.
 */
class SongTitleField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Song::ATTRIBUTE_TITLE);
    }
}
