<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Audio;

use App\GraphQL\Schema\Fields\IntField;
use App\Models\Wiki\Audio;

class AudioSizeField extends IntField
{
    public function __construct()
    {
        parent::__construct(Audio::ATTRIBUTE_SIZE, nullable: false);
    }

    public function description(): string
    {
        return 'The size of the file in storage in Bytes';
    }
}
