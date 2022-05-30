<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Search;

use App\Http\Api\Field\Field;
use App\Http\Resources\Wiki\Collection\VideoCollection;

/**
 * Class SearchVideoField.
 */
class SearchVideoField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(VideoCollection::$wrap);
    }
}
