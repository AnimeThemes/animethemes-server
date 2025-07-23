<?php

declare(strict_types=1);

namespace App\Filament\Components\Fields;

use App\Filament\StateCasts\UriStateCast;
use Filament\Forms\Components\TextInput as BaseTextInput;

/**
 * Class TextInput.
 */
class TextInput extends BaseTextInput
{
    /**
     * Set the field to be a URI input.
     *
     * @return static
     */
    public function uri(): static
    {
        return $this
            ->stateCast(app(UriStateCast::class))
            ->url();
    }
}
