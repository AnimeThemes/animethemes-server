<?php

declare(strict_types=1);

namespace App\Filament\Components\Fields;

class Slug extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->required();
        $this->maxLength(192);
        $this->unique();
        $this->alphaDash();
    }
}
