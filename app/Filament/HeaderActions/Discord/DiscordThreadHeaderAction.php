<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Discord;

use App\Actions\Discord\DiscordThreadAction as DiscordThreadActionAction;
use App\Models\Wiki\Anime;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Actions\Action;

/**
 * Class DiscordThreadAction.
 */
class DiscordThreadHeaderAction extends Action
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->fillForm(fn (Anime $record): array => ['name' => $record->getName()]);

        $this->action(fn (Anime $record, array $data) => (new DiscordThreadActionAction())->handle($record, $data));
    }
    
    /**
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form
     */
    public function getForm(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('filament.actions.discord.thread.name'))
                    ->helperText(__('filament.actions.discord.thread.help'))
                    ->required()
                    ->maxlength(100)
                    ->rules(['required', 'max:100']),
            ]);
    }
}