<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Config\Wiki;

use App\Constants\Config\WikiConstants;
use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;

/**
 * Class WikiFeaturedThemeField.
 */
class WikiFeaturedThemeField extends Field
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, WikiConstants::FEATURED_THEME_SETTING);
    }
}
