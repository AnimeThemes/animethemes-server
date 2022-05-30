<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Base;

use App\Http\Api\Field\DateField;
use App\Models\BaseModel;

/**
 * Class UpdatedAtField.
 */
class UpdatedAtField extends DateField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(BaseModel::ATTRIBUTE_UPDATED_AT);
    }
}
