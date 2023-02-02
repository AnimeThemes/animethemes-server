<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Anime;

use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;
use App\Pivots\Wiki\AnimeResource;

/**
 * Class AnimeAsField.
 */
class AnimeAsField extends Field
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, AnimeResource::ATTRIBUTE_AS);
    }
}
