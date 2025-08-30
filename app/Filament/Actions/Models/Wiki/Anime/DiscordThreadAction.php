<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Anime;

use App\Actions\Discord\DiscordThreadAction as DiscordThreadActionAction;
use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\TextInput;
use App\Models\Discord\DiscordThread;
use App\Models\Wiki\Anime;
use Exception;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;

class DiscordThreadAction extends BaseAction
{
    public static function getDefaultName(): ?string
    {
        return 'discord-thread';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.anime.discord_thread.name'));
        $this->icon(Heroicon::OutlinedChatBubbleLeftRight);

        $this->visible(Gate::allows('create', DiscordThread::class));

        $this->fillForm(fn (Anime $record): array => ['name' => $record->getName()]);

        $this->action(function (Anime $record, array $data) {
            $action = new DiscordThreadActionAction()->handle($record, $data);

            if ($action instanceof Exception) {
                $this->failedLog($action);
            }
        });
    }

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
