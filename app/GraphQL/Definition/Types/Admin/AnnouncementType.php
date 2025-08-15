<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Admin;

use App\GraphQL\Definition\Fields\Admin\Announcement\AnnouncementContentField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\EloquentType;
use App\Models\Admin\Announcement;

class AnnouncementType extends EloquentType
{
    /**
     * The description of the type.
     */
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
