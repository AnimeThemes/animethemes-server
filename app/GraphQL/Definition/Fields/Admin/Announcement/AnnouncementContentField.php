<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Admin\Announcement;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Admin\Announcement;

class AnnouncementContentField extends StringField
{
    public function __construct()
    {
        parent::__construct(Announcement::ATTRIBUTE_CONTENT, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The announcement text';
    }
}
