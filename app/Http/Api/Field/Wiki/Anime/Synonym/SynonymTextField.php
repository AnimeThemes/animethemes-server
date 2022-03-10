<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime\Synonym;

use App\Http\Api\Field\StringField;
use App\Models\Wiki\Anime\AnimeSynonym;

/**
 * Class SynonymTextField.
 */
class SynonymTextField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeSynonym::ATTRIBUTE_TEXT);
    }
}
