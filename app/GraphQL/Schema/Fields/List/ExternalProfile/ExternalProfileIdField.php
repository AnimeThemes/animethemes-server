<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\List\ExternalProfile;

use App\Contracts\GraphQL\Fields\BindableField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\Models\List\ExternalProfile;

class ExternalProfileIdField extends IdField implements BindableField
{
    public function __construct()
    {
        parent::__construct(ExternalProfile::ATTRIBUTE_ID, ExternalProfile::class);
    }

    /**
     * The resolver to cast the model.
     */
    public function bindResolver(array $args): null
    {
        return null;
    }
}
