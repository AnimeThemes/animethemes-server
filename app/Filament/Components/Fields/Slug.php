<?php

declare(strict_types=1);

namespace App\Filament\Components\Fields;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

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
        $this->unique(ignoreRecord: true);
        $this->rules([
            fn (?Model $record) => [
                'required',
                'max:192',
                'alpha_dash',
                $record !== null
                    ? Rule::unique($record::class, $this->getName())
                        ->ignore($record->getKey(), $record->getKeyName())
                        ->__toString()
                    : Rule::unique($this->getModel(), $this->getName())->__toString(),
            ],
        ]);
    }
}
