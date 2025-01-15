<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Models\Discord;

use App\Actions\Discord\DiscordMessageAction;
use App\Discord\DiscordEmbed;
use App\Discord\DiscordMessage;
use App\Filament\TableActions\BaseTableAction;
use App\Models\Discord\DiscordThread;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

/**
 * Class DiscordSendMessageTableAction.
 */
class DiscordSendMessageTableAction extends BaseTableAction
{
    /**
    * Initial setup for the action.
    *
    * @return void
    */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.table_actions.discord_thread.message.send.name'));
        $this->icon(__('filament-icons.table_actions.discord_thread.message.send'));

        $this->authorize('forcedeleteany', DiscordThread::class);
    }

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
                TextInput::make(DiscordMessage::ATTRIBUTE_CHANNEL_ID)
                    ->label(__('filament.table_actions.discord_thread.message.channelId.name'))
                    ->helperText(__('filament.table_actions.discord_thread.message.channelId.help'))
                    ->required()
                    ->rules(['required', 'string']),

                RichEditor::make(DiscordMessage::ATTRIBUTE_CONTENT)
                    ->label(__('filament.table_actions.discord_thread.message.content.name'))
                    ->helperText(__('filament.table_actions.discord_thread.message.content.help')),

                Repeater::make(DiscordMessage::ATTRIBUTE_EMBEDS)
                    ->label(__('filament.table_actions.discord_thread.message.embeds.name'))
                    ->addActionLabel(__('filament.buttons.add'))
                    ->helperText(__('filament.table_actions.discord_thread.message.embeds.help'))
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
                            ->addActionLabel(__('filament.buttons.add'))
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
                    ->addActionLabel(__('filament.buttons.add'))
                    ->helperText(__('filament.table_actions.discord_thread.message.images.help'))
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
