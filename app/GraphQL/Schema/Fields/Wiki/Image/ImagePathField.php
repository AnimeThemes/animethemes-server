<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Image;

use App\GraphQL\Schema\Fields\StringField;
use App\Models\Wiki\Image;

class ImagePathField extends StringField
{
    public function __construct()
    {
        parent::__construct(Image::ATTRIBUTE_PATH, nullable: false);
    }

    public function description(): string
    {
        return 'The path of the file in storage';
    }
}
