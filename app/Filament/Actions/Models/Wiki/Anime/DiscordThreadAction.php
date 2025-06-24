<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Anime;

use App\Actions\Discord\DiscordThreadAction as DiscordThreadActionAction;
use App\Filament\Actions\BaseAction;
use App\Models\Discord\DiscordThread;
use App\Models\Wiki\Anime;
use Exception;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Class DiscordThreadAction.
 */
class DiscordThreadAction extends BaseAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.anime.discord_thread.name'));
        $this->icon(__('filament-icons.actions.anime.discord_thread'));

        $this->authorize('create', DiscordThread::class);

        $this->fillForm(fn (Anime $record): array => ['name' => $record->getName()]);

        $this->action(function (Anime $record, array $data) {
            $action = new DiscordThreadActionAction()->handle($record, $data);

            if ($action instanceof Exception) {
                $this->failedLog($action);
            }
        });
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
                TextInput::make('name')
                    ->label(__('filament.actions.discord.thread.name'))
                    ->helperText(__('filament.actions.discord.thread.help'))
                    ->required()
                    ->maxlength(100),
            ]);
    }
}
