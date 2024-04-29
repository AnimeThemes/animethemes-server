<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Anime\Synonym;

use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Scout\Elasticsearch\Api\Field\EnumField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

/**
 * Class SynonymTypeField.
 */
class SynonymTypeField extends EnumField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeSynonym::ATTRIBUTE_TYPE, AnimeSynonymType::class);
    }
}
