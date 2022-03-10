<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Search;

use App\Http\Api\Field\Field;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;

/**
 * Class SearchThemeField.
 */
class SearchThemeField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ThemeCollection::$wrap);
    }
}
