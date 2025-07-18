<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL\Fields;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface BindableField.
 */
interface BindableField
{
    /**
     * Get the model that the field should bind to.
     *
     * @return class-string<Model>
     */
    public function bindTo(): string;

    /**
     * Get the column that the field should use to bind.
     *
     * @return string
     */
    public function bindUsingColumn(): string;
}
