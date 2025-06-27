<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Discord;

use App\Actions\Discord\DiscordMessageAction;
use App\Discord\DiscordEmbed;
use App\Discord\DiscordEmbedField;
use App\Discord\DiscordMessage;
use App\Filament\Actions\BaseAction;
use App\Models\Discord\DiscordThread;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * Class DiscordEditMessageAction.
 */
class DiscordEditMessageAction extends BaseAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.table_actions.discord_thread.message.edit.name'));
        $this->icon(__('filament-icons.table_actions.discord_thread.message.edit'));

        $this->visible(Auth::user()->can('deleteany', DiscordThread::class));
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

        $action->edit($message);
    }

    /**
     * Get the fields available on the action.
     *
     * @param  Schema  $schema
     * @return Schema
     */
    public function getSchema(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(DiscordMessage::ATTRIBUTE_URL)
                    ->label(__('filament.table_actions.discord_thread.message.url.name'))
                    ->helperText(__('filament.table_actions.discord_thread.message.url.help'))
                    ->required()
                    ->autofocus()
                    ->regex('/https:\/\/discord\.com\/channels\/\d+\/\d+\/\d+/')
                    ->hintAction(
                        Action::make('load')
                            ->label(__('filament.table_actions.discord_thread.message.url.action'))
                            ->action(function (Set $set, string $state, TextInput $component) {
                                if (! preg_match($component->getRegexPattern(), $state)) {
                                    $component
                                        ->hint(__('filament.table_actions.discord_thread.message.url.validation'))
                                        ->hintColor('danger');

                                    return;
                                }

                                $component->hint(null);

                                $message = new DiscordMessageAction()->get($state);

                                $set(DiscordMessage::ATTRIBUTE_CONTENT, $message->getContent());

                                foreach ($message->getEmbeds() as $index => $embed) {
                                    foreach ($embed->toArray() as $key => $value) {
                                        if ($key === DiscordEmbed::ATTRIBUTE_COLOR) {
                                            $value = '#'.dechex($value);
                                        }
                                        if ($key === DiscordEmbed::ATTRIBUTE_THUMBNAIL || $key === DiscordEmbed::ATTRIBUTE_IMAGE) {
                                            $value = Arr::get($value, 'url');
                                        }

                                        $set("embeds.item{$index}.{$key}", $value);
                                    }

                                    foreach ($embed->getFields() as $fieldIndex => $field) {
                                        foreach ($field->toArray(false) as $key => $value) {
                                            $set("embeds.item{$index}.fields.{$fieldIndex}.{$key}", $value);
                                        }
                                    }
                                }

                                foreach ($message->getImages() as $index => $file) {
                                    $set("images.item{$index}.url", $file);
                                }
                            })
                    ),

                Textarea::make(DiscordMessage::ATTRIBUTE_CONTENT)
                    ->label(__('filament.table_actions.discord_thread.message.content.name'))
                    ->helperText(__('filament.table_actions.discord_thread.message.content.help')),

                Repeater::make(DiscordMessage::ATTRIBUTE_EMBEDS)
                    ->label(__('filament.table_actions.discord_thread.message.embeds.name'))
                    ->addActionLabel(__('filament.buttons.add', ['label' => null]))
                    ->helperText(__('filament.table_actions.discord_thread.message.embeds.help'))
                    ->key(DiscordMessage::ATTRIBUTE_EMBEDS)
                    ->collapsible()
                    ->defaultItems(0)
                    ->schema([
                        TextInput::make(DiscordEmbed::ATTRIBUTE_TITLE)
                            ->label(__('filament.table_actions.discord_thread.message.embeds.body.title.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.title.help')),

                        Textarea::make(DiscordEmbed::ATTRIBUTE_DESCRIPTION)
                            ->label(__('filament.table_actions.discord_thread.message.embeds.body.description.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.description.help'))
                            ->required(),

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
                            ->addActionLabel(__('filament.buttons.add', ['label' => null]))
                            ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.fields.title.help'))
                            ->collapsible()
                            ->schema([
                                TextInput::make(DiscordEmbedField::ATTRIBUTE_NAME)
                                    ->label(__('filament.table_actions.discord_thread.message.embeds.body.fields.name.name'))
                                    ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.fields.name.help'))
                                    ->required(),

                                TextInput::make(DiscordEmbedField::ATTRIBUTE_VALUE)
                                    ->label(__('filament.table_actions.discord_thread.message.embeds.body.fields.value.name'))
                                    ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.fields.value.help'))
                                    ->required(),

                                Checkbox::make(DiscordEmbedField::ATTRIBUTE_INLINE)
                                    ->label(__('filament.table_actions.discord_thread.message.embeds.body.fields.inline.name'))
                                    ->helperText(__('filament.table_actions.discord_thread.message.embeds.body.fields.inline.help')),
                            ]),
                    ]),

                Repeater::make(DiscordMessage::ATTRIBUTE_IMAGES)
                    ->label(__('filament.table_actions.discord_thread.message.images.name'))
                    ->addActionLabel(__('filament.buttons.add', ['label' => null]))
                    ->helperText(__('filament.table_actions.discord_thread.message.images.help'))
                    ->key(DiscordMessage::ATTRIBUTE_IMAGES)
                    ->collapsible()
                    ->defaultItems(0)
                    ->schema([
                        TextInput::make(DiscordMessage::ATTRIBUTE_URL)
                            ->label(__('filament.table_actions.discord_thread.message.images.body.url.name'))
                            ->helperText(__('filament.table_actions.discord_thread.message.images.body.url.help'))
                            ->required(),
                    ]),
            ]);
    }
}
