<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\ExternalProfile;

use App\Enums\Models\List\ExternalProfileSite;
use App\GraphQL\Definition\Fields\EnumField;
use App\Models\List\ExternalProfile;

class ExternalProfileSiteField extends EnumField
{
    public function __construct()
    {
        parent::__construct(ExternalProfile::ATTRIBUTE_SITE, ExternalProfileSite::class, nullable: false);
    }

    public function description(): string
    {
        return 'The site the profile belongs to';
    }
}
