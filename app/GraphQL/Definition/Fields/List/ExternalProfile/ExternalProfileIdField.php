<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\List\ExternalProfile;

use App\Contracts\GraphQL\Fields\BindableField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\Models\List\ExternalProfile;
use Illuminate\Database\Eloquent\Model;

class ExternalProfileIdField extends IdField implements BindableField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalProfile::ATTRIBUTE_ID);
    }

    /**
     * Get the model that the field should bind to.
     *
     * @return class-string<Model>
     */
    public function bindTo(): string
    {
        return ExternalProfile::class;
    }

    /**
     * Get the column that the field should use to bind.
     *
     * @return string
     */
    public function bindUsingColumn(): string
    {
        return $this->column;
    }
}
