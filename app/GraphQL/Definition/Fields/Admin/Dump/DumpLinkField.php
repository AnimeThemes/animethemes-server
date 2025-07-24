<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Admin\Dump;

use App\GraphQL\Definition\Fields\StringField;

class DumpLinkField extends StringField
{
    final public const FIELD = 'link';

    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(self::FIELD, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The URL to download the file from storage';
    }

    /**
     * Determine if the field is nullable.
     *
     * @return bool
     */
    protected function nullable(): bool
    {
        return false;
    }
}
