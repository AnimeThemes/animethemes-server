<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Synonym;

use App\Models\Wiki\Anime\AnimeSynonym;
use App\Scout\Elasticsearch\Api\Field\StringField;

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
