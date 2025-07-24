<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Admin\Dump;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Admin\Dump;

class DumpPathField extends StringField
{
    public function __construct()
    {
        parent::__construct(Dump::ATTRIBUTE_PATH, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The path of the file in storage';
    }

    /**
     * Determine if the field is nullable.
     */
    protected function nullable(): bool
    {
        return false;
    }
}
