<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Admin\Announcement;

use App\Http\Api\Field\StringField;
use App\Models\Admin\Announcement;

/**
 * Class AnnouncementContentField.
 */
class AnnouncementContentField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(Announcement::ATTRIBUTE_CONTENT);
    }
}
