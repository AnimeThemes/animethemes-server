<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Wiki\SongResoure;

use App\GraphQL\Definition\Fields\StringField;
use App\Pivots\Wiki\SongResource;

class SongResourceAsField extends StringField
{
    public function __construct()
    {
        parent::__construct(SongResource::ATTRIBUTE_AS);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'Used to distinguish resources that map to the same song';
    }
}
