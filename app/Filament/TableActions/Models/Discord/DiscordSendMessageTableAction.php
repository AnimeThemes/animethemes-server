<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Models\Discord;

use App\Actions\Discord\DiscordMessageAction;
use App\Filament\TableActions\BaseTableAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

/**
 * Class DiscordSendMessageTableAction.
 */
class DiscordSendMessageTableAction extends BaseTableAction
{
    /**
     * Perform the action on the table.
     *
     * @param  array  $fields
     * @return void
     */
    public function handle(array $fields): void
    {
        $action = new DiscordMessageAction();

        $message = $action->makeMessage($fields);

        $action->send($message);
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
                TextInput::make('channelId')
                    ->label(__('filament.table_actions.discord_thread.message.channelId.name'))
                    ->helperText(__('filament.table_actions.discord_thread.message.channelId.help'))
                    ->required()
                    ->rules(['required', 'string']),

                MarkdownEditor::make('content')
                    ->label(__('filament.table_actions.discord_thread.message.content.name'))
                    ->helperText(__('filament.table_actions.discord_thread.message.content.help')),

                Repeater::make('embeds')
                    ->label(__('filament.table_actions.discord_thread.message.embeds.name'))
                    ->helperText(__('filament.table_actions.discord_thread.message.embeds.help'))
                    ->key('embeds')
                    ->collapsible()
                    ->schema([
                        TextInput::make('title')
                            ->label(__('filament.table_actions.discord_thread.message.embeds.body.title.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.title.help')),

                        MarkdownEditor::make('description')
                            ->label(__('filament.table_actions.discord_thread.message.embeds.body.description.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.description.help'))
                            ->required()
                            ->rules(['required', 'string']),

                        ColorPicker::make('color')
                            ->label(__('filament.table_actions.discord_thread.message.embeds.body.color.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.color.help')),

                        Repeater::make('fields')
                            ->label(__('filament.table_actions.discord_thread.message.embeds.body.fields.title.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.fields.title.help'))
                            ->collapsible()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('filament.table_actions.discord_thread.message.embeds.body.fields.name.name'))
                                    ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.fields.name.help'))
                                    ->required()
                                    ->rules(['required', 'string']),

                                TextInput::make('value')
                                    ->label(__('filament.table_actions.discord_thread.message.embeds.body.fields.value.name'))
                                    ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.fields.value.help'))
                                    ->required()
                                    ->rules(['required', 'string']),

                                Checkbox::make('inline')
                                    ->label(__('filament.table_actions.discord_thread.message.embeds.body.fields.inline.name'))
                                    ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.fields.inline.help')),
                            ]),
                    ]),
            ]);
    }
}
