<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Document;

use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Document\Page\PageBodyField;
use App\GraphQL\Definition\Fields\Document\Page\PageNameField;
use App\GraphQL\Definition\Fields\Document\Page\PageSlugField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\EloquentType;
use App\Models\Document\Page;

/**
 * Class PageType.
 */
class PageType extends EloquentType implements HasFields
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents a static markdown page used for guides and other documentation.\n\nFor example, the 'encoding/audio_normalization' page represents the documentation for audio normalization.";
    }

    /**
     * The fields of the type.
     *
     * @return array<int, Field>
     */
    public function fields(): array
    {
        return [
            new IdField(Page::ATTRIBUTE_ID),
            new PageNameField(),
            new PageSlugField(),
            new PageBodyField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
