<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Admin\Dump;

use App\Contracts\GraphQL\Fields\DisplayableField;
use App\GraphQL\Schema\Fields\Field;
use App\Models\Admin\Dump;
use GraphQL\Type\Definition\Type;

class DumpLinkField extends Field implements DisplayableField
{
    public function __construct()
    {
        parent::__construct(Dump::ATTRIBUTE_LINK, nullable: false);
    }

    public function description(): string
    {
        return 'The URL to download the file from storage';
    }

    public function baseType(): Type
    {
        return Type::string();
    }

    public function canBeDisplayed(): bool
    {
        return true;
    }
}
