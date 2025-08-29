<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Models\Wiki\Video;

use App\Actions\Discord\DiscordVideoNotificationAction as DiscordVideoNotificationActionAction;
use App\Enums\Actions\Models\Wiki\Video\DiscordNotificationType;
use App\Filament\BulkActions\BaseBulkAction;
use App\Filament\Components\Fields\Select;
use App\Models\Discord\DiscordThread;
use App\Models\Wiki\Video;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class VideoDiscordNotificationBulkAction extends BaseBulkAction
{
    /**
     * The default name of the action.
     */
    public static function getDefaultName(): ?string
    {
        return 'video-discord-notification-bulk';
    }

    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->modalWidth(Width::Large);

        $this->label(__('filament.bulk_actions.discord.notification.name'));
        $this->icon(Heroicon::OutlinedBell);

        $this->visible(Gate::allows('create', DiscordThread::class));
    }

    /**
     * Handle the action.
     *
     * @param  Collection<int, Video>  $videos
     * @param  array<string, mixed>  $data
     */
    public function handle(Collection $videos, array $data): void
    {
        $videos = $videos->sortBy(Video::ATTRIBUTE_ID);

        $action = new DiscordVideoNotificationActionAction();

        $action->handle($videos, $data);
    }

    /**
     * Get the form for the action.
     */
    public function getSchema(Schema $schema): ?Schema
    {
        return $schema
            ->components([
                Select::make(DiscordNotificationType::getFieldKey())
                    ->label(__('filament.bulk_actions.discord.notification.type.name'))
                    ->helperText(__('filament.bulk_actions.discord.notification.type.help'))
                    ->options(DiscordNotificationType::class)
                    ->default(DiscordNotificationType::ADDED)
                    ->required(),
            ]);
    }
}
