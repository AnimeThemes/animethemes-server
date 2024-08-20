<?php

declare(strict_types=1);

namespace App\Filament\Components\Fields;

use Filament\Forms\Components\TextInput;

/**
 * Class Slug.
 */
class Slug extends TextInput
{
    /**
     * Initial setup for the field.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->required();
        $this->maxLength(192);
        $this->unique(ignoreRecord: true);
        $this->alphaDash();
    }
}
