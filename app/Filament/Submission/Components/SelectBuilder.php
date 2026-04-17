<?php

declare(strict_types=1);

namespace App\Filament\Submission\Components;

use App\Filament\Components\Fields\Select;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SelectBuilder extends Builder
{
    /**
     * @param  class-string<Model>  $model
     * @param  class-string  $form
     */
    public function set(string $model, string $form): static
    {
        $singularLabel = Str::afterLast($this->getAddActionLabel(), ' ');

        $this->blockNumbers(false);

        $this->blocks([
            Block::make('create')
                ->label(__('filament.buttons.create', ['label' => $singularLabel]))
                ->schema(fn (Schema $schema) => $form::configure($schema)->getComponents()),

            Block::make('attach')
                ->label(__('filament.buttons.attach', ['label' => $singularLabel]))
                ->schema(fn ($livewire): array => [
                    Select::make('id')
                        ->label($singularLabel)
                        ->useScout($livewire, $model),
                ]),
        ]);

        return $this;
    }
}
