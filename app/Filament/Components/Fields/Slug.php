<?php

declare(strict_types=1);

namespace App\Filament\Components\Fields;

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
        $this->unique();
        $this->alphaDash();
    }
}
