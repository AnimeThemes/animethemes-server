<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Admin;

use App\GraphQL\Schema\Fields\Admin\Announcement\AnnouncementContentField;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\EloquentType;
use App\Models\Admin\Announcement;

class AnnouncementType extends EloquentType
{
    public function description(): string
    {
        return 'Represents a site-wide message to be broadcasted on the homepage.';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new IdField(Announcement::ATTRIBUTE_ID, Announcement::class),
            new AnnouncementContentField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
