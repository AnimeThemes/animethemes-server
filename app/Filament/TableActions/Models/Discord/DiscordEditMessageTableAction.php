<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Models\Discord;

use App\Actions\Discord\DiscordMessageAction;
use App\Discord\DiscordEmbed;
use App\Discord\DiscordMessage;
use App\Filament\TableActions\BaseTableAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Illuminate\Support\Arr;

/**
 * Class DiscordEditMessageTableAction.
 */
class DiscordEditMessageTableAction extends BaseTableAction
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

        $action->edit($message);
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
                TextInput::make(DiscordMessage::ATTRIBUTE_URL)
                    ->label(__('filament.table_actions.discord_thread.message.url.name'))
                    ->helperText(__('filament.table_actions.discord_thread.message.url.help'))
                    ->required()
                    ->autofocus()
                    ->regex('/https:\/\/discord\.com\/channels\/\d+\/\d+\/\d+/')
                    ->rules(['required', 'string', 'regex:/https:\/\/discord\.com\/channels\/\d+\/\d+\/\d+/'])
                    ->hintAction(
                        Action::make('load')
                            ->label(__('filament.table_actions.discord_thread.message.url.action'))
                            ->action(function (Set $set, string $state, TextInput $component) {
                                if (!preg_match($component->getRegexPattern(), $state)) {
                                    $component
                                        ->hint(__('filament.table_actions.discord_thread.message.url.validation'))
                                        ->hintColor('danger');
                                    return;
                                }

                                $component->hint(null);

                                $message = (new DiscordMessageAction())->get($state)->getMessage();

                                $set(DiscordMessage::ATTRIBUTE_CONTENT, Arr::get($message, DiscordMessage::ATTRIBUTE_CONTENT));

                                foreach (Arr::get($message, DiscordMessage::ATTRIBUTE_EMBEDS) ?? [] as $index => $embed) {
                                    foreach ($embed as $key => $value) {
                                        $set("embeds.item{$index}.{$key}", $key === DiscordEmbed::ATTRIBUTE_COLOR ? '#' . dechex($value) : $value);
                                    }

                                    foreach (Arr::get($embed, DiscordEmbed::ATTRIBUTE_FIELDS) ?? [] as $fieldIndex => $field) {
                                        foreach ($field as $key => $value) {
                                            $set("embeds.item{$index}.fields.{$fieldIndex}.{$key}", $value);
                                        }
                                    }
                                }

                                foreach (Arr::get($message, 'files') as $index => $file) {
                                    $set("images.item{$index}.url", $file);
                                }
                            })
                    ),

                RichEditor::make(DiscordMessage::ATTRIBUTE_CONTENT)
                    ->label(__('filament.table_actions.discord_thread.message.content.name'))
                    ->helperText(__('filament.table_actions.discord_thread.message.content.help')),

                Repeater::make(DiscordMessage::ATTRIBUTE_EMBEDS)
                    ->label(__('filament.table_actions.discord_thread.message.embeds.name'))
                    ->helperText(__('filament.table_actions.discord_thread.message.embeds.help'))
                    ->key(DiscordMessage::ATTRIBUTE_EMBEDS)
                    ->collapsible()
                    ->defaultItems(0)
                    ->schema([
                        TextInput::make(DiscordEmbed::ATTRIBUTE_TITLE)
                            ->label(__('filament.table_actions.discord_thread.message.embeds.body.title.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.title.help')),

                        RichEditor::make(DiscordEmbed::ATTRIBUTE_DESCRIPTION)
                            ->label(__('filament.table_actions.discord_thread.message.embeds.body.description.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.description.help'))
                            ->required()
                            ->rules(['required', 'string']),

                        ColorPicker::make(DiscordEmbed::ATTRIBUTE_COLOR)
                            ->label(__('filament.table_actions.discord_thread.message.embeds.body.color.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.color.help')),

                        TextInput::make(DiscordEmbed::ATTRIBUTE_THUMBNAIL)
                            ->label(__('filament.table_actions.discord_thread.message.embeds.body.thumbnail.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.thumbnail.help')),

                        TextInput::make(DiscordEmbed::ATTRIBUTE_IMAGE)
                            ->label(__('filament.table_actions.discord_thread.message.embeds.body.image.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.image.help')),

                        Repeater::make(DiscordEmbed::ATTRIBUTE_FIELDS)
                            ->label(__('filament.table_actions.discord_thread.message.embeds.body.fields.title.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.fields.title.help'))
                            ->collapsible()
                            ->schema([
                                TextInput::make(DiscordEmbed::ATTRIBUTE_FIELDS_NAME)
                                    ->label(__('filament.table_actions.discord_thread.message.embeds.body.fields.name.name'))
                                    ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.fields.name.help'))
                                    ->required()
                                    ->rules(['required', 'string']),

                                TextInput::make(DiscordEmbed::ATTRIBUTE_FIELDS_VALUE)
                                    ->label(__('filament.table_actions.discord_thread.message.embeds.body.fields.value.name'))
                                    ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.fields.value.help'))
                                    ->required()
                                    ->rules(['required', 'string']),

                                Checkbox::make(DiscordEmbed::ATTRIBUTE_FIELDS_INLINE)
                                    ->label(__('filament.table_actions.discord_thread.message.embeds.body.fields.inline.name'))
                                    ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.fields.inline.help')),
                            ]),
                    ]),

                Repeater::make(DiscordMessage::ATTRIBUTE_IMAGES)
                    ->label(__('filament.table_actions.discord_thread.message.images.name'))
                    ->helperText(__('filament.table_actions.discord_thread.message.images.help'))
                    ->key(DiscordMessage::ATTRIBUTE_IMAGES)
                    ->collapsible()
                    ->defaultItems(0)
                    ->schema([
                        TextInput::make(DiscordMessage::ATTRIBUTE_URL)
                            ->label(__('filament.table_actions.discord_thread.message.images.body.url.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.images.body.url.help'))
                            ->required()
                            ->rules(['required', 'string']),
                    ]),
            ]);
    }
}
