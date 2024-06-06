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
                TextInput::make('url')
                    ->label(__('filament.table_actions.discord_thread.message.url.name'))
                    ->helperText(__('filament.table_actions.discord_thread.message.url.help'))
                    ->live(true)
                    ->required()
                    ->rules(['required', 'string'])
                    ->afterStateUpdated(function (Set $set, TextInput $component, string $state) {
                        $message = (new DiscordMessageAction())->get($state)->getMessage();

                        $set('content', Arr::get($message, 'content'));

                        $index = 0;
                        foreach (Arr::get($message, 'embeds') ?? [] as $embed) {
                            $set("embeds.item{$index}.title", Arr::get($embed, 'title'));
                            $set("embeds.item{$index}.description", Arr::get($embed, 'description'));
                            $set("embeds.item{$index}.color", '#' . dechex(Arr::get($embed, 'color')));
                            $set("embeds.item{$index}.thumbnail", Arr::get($embed, 'thumbnail'));
                            $set("embeds.item{$index}.image", Arr::get($embed, 'image'));

                            $fieldIndex = 0;
                            foreach (Arr::get($embed, 'fields') ?? [] as $field) {
                                $set("embeds.item{$index}.fields.{$fieldIndex}.name", Arr::get($field, 'name'));
                                $set("embeds.item{$index}.fields.{$fieldIndex}.value", Arr::get($field, 'value'));
                                $set("embeds.item{$index}.fields.{$fieldIndex}.inline", Arr::get($field, 'inline'));
                                $fieldIndex++;
                            }
                            $index++;
                        }

                        $index = 0;
                        foreach (Arr::get($message, 'files') as $file) {
                            $set("images.item{$index}.url", $file);
                            $index++;
                        }
                    }),

                MarkdownEditor::make('content')
                    ->label(__('filament.table_actions.discord_thread.message.content.name'))
                    ->helperText(__('filament.table_actions.discord_thread.message.content.help')),

                Repeater::make('embeds')
                    ->label(__('filament.table_actions.discord_thread.message.embeds.name'))
                    ->helperText(__('filament.table_actions.discord_thread.message.embeds.help'))
                    ->key('embeds')
                    ->collapsible()
                    ->defaultItems(0)
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

                        TextInput::make('thumbnail')
                            ->label(__('filament.table_actions.discord_thread.message.embeds.body.thumbnail.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.thumbnail.help')),

                        TextInput::make('image')
                            ->label(__('filament.table_actions.discord_thread.message.embeds.body.image.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.image.help')),

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

                Repeater::make('images')
                    ->label(__('filament.table_actions.discord_thread.message.images.name'))
                    ->helperText(__('filament.table_actions.discord_thread.message.images.help'))
                    ->key('images')
                    ->collapsible()
                    ->defaultItems(0)
                    ->schema([
                        TextInput::make('url')
                            ->label(__('filament.table_actions.discord_thread.message.images.body.url.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.images.body.url.help'))
                            ->required()
                            ->rules(['required', 'string']),
                    ]),
            ]);
    }
}
