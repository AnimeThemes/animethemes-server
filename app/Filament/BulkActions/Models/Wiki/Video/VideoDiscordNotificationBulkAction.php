<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Models\Wiki\Video;

use App\Actions\Discord\DiscordVideoNotificationAction as DiscordVideoNotificationActionAction;
use App\Enums\Actions\Models\Wiki\Video\DiscordNotificationType;
use App\Filament\BulkActions\BaseBulkAction;
use App\Filament\Components\Fields\Select;
use App\Models\Discord\DiscordThread;
use App\Models\Wiki\Video;
use Filament\Forms\Form;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class VideoDiscordNotificationBulkAction.
 */
class VideoDiscordNotificationBulkAction extends BaseBulkAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->modalWidth(MaxWidth::Large);

        $this->label(__('filament.bulk_actions.discord.notification.name'));
        $this->icon(__('filament-icons.bulk_actions.discord.notification'));

        $this->authorize('create', DiscordThread::class);
    }

    /**
     * Handle the action.
     *
     * @param  Collection<int, Video>  $videos
     * @param  array  $fields
     * @return void
     */
    public function handle(Collection $videos, array $fields): void
    {
        $videos = $videos->sortBy(Video::ATTRIBUTE_ID);

        $action = new DiscordVideoNotificationActionAction();

        $action->handle($videos, $fields);
    }

    /**
     * Get the form for the action.
     *
     * @param  Form  $form
     * @return Form
     */
    public function getForm(Form $form): ?Form
    {
        return $form
            ->schema([
                Select::make(DiscordNotificationType::getFieldKey())
                    ->label(__('filament.bulk_actions.discord.notification.type.name'))
                    ->helperText(__('filament.bulk_actions.discord.notification.type.help'))
                    ->options(DiscordNotificationType::asSelectArray())
                    ->default(DiscordNotificationType::ADDED->value)
                    ->required()
                    ->enum(DiscordNotificationType::class),
            ]);
    }
}
