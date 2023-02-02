<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Wiki\Studio;

use App\Models\Wiki\Studio;
use App\Scout\Elasticsearch\Api\Field\StringField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

/**
 * Class StudioSlugField.
 */
class StudioSlugField extends StringField
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Studio::ATTRIBUTE_SLUG);
    }
}
