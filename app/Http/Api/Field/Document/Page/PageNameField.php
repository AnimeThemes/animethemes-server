<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Document\Page;

use App\Http\Api\Field\StringField;
use App\Models\Document\Page;

/**
 * Class PageNameField.
 */
class PageNameField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Page::ATTRIBUTE_NAME);
    }
}
