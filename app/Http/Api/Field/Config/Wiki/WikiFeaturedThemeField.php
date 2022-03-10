<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Config\Wiki;

use App\Constants\Config\WikiConstants;
use App\Http\Api\Field\Field;

/**
 * Class WikiFeaturedThemeField.
 */
class WikiFeaturedThemeField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(WikiConstants::FEATURED_THEME_SETTING);
    }
}
