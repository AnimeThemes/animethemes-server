<?php

declare(strict_types=1);

namespace App\Filament\Components\Infolist;

use Closure;
use Filament\Infolists\Components\Entry;

class KeyValueThreeEntry extends Entry
{
    /**
     * @var view-string
     */
    protected string $view = 'filament.infolist.key-value-three-entry';

    protected string|Closure|null $leftLabel = null;
    protected string|Closure|null $middleLabel = null;
    protected string|Closure|null $rightLabel = null;
    protected array|Closure|null $middleValueThroughState = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->placeholder(__('filament-infolists::components.entries.key_value.placeholder'));
    }

    public function middleValueThroughState(array|Closure|null $state): static
    {
        $this->middleValueThroughState = $state;

        return $this;
    }

    public function getMiddleValueThroughState(): mixed
    {
        return $this->evaluate($this->middleValueThroughState);
    }

    public function leftLabel(string|Closure|null $label): static
    {
        $this->leftLabel = $label;

        return $this;
    }

    public function middleLabel(string|Closure|null $label): static
    {
        $this->middleLabel = $label;

        return $this;
    }

    public function rightLabel(string|Closure|null $label): static
    {
        $this->rightLabel = $label;

        return $this;
    }

    public function getLeftLabel(): string
    {
        return $this->evaluate($this->leftLabel) ?? __('filament-infolists::components.entries.key_value.columns.key.label');
    }

    public function getMiddleLabel(): string
    {
        return $this->evaluate($this->middleLabel) ?? __('filament-infolists::components.entries.key_value.columns.value.label');
    }

    public function getRightLabel(): string
    {
        return $this->evaluate($this->rightLabel) ?? __('filament-infolists::components.entries.key_value.columns.value.label');
    }
}
