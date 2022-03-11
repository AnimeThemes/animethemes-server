<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Song;

use App\Http\Api\Field\StringField;
use App\Models\Wiki\Song;

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
