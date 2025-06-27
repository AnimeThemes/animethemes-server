<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Admin;

use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Fields\Admin\Announcement\AnnouncementContentField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\EloquentType;
use App\Models\Admin\Announcement;

/**
 * Class AnnouncementType.
 */
class AnnouncementType extends EloquentType implements HasFields
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Represents a site-wide message to be broadcasted on the homepage.';
    }

    /**
     * The fields of the type.
     *
     * @return array<int, Field>
     */
    public function fields(): array
    {
        return [
            new IdField(Announcement::ATTRIBUTE_ID),
            new AnnouncementContentField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
