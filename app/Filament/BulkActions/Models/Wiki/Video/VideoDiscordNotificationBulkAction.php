<?php

declare(strict_types=1);

namespace App\Filament\BulkActions\Models\Wiki\Video;

use App\Actions\Discord\DiscordVideoNotificationAction as DiscordVideoNotificationActionAction;
use App\Filament\BulkActions\BaseBulkAction;
use App\Filament\Components\Fields\Select;
use App\Models\Discord\DiscordThread;
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

        $this->authorize('create', DiscordThread::class);
    }

    /**
     * Handle the action.
     *
     * @param  Collection  $videos
     * @param  array  $fields
     */
    public function handle(Collection $videos, array $fields): void
    {
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
                Select::make('type')
                    ->label(__('filament.bulk_actions.discord.notification.type.name'))
                    ->helperText(__('filament.bulk_actions.discord.notification.type.help'))
                    ->options([
                        'added' => __('filament.bulk_actions.discord.notification.type.options.added'),
                        'updated' => __('filament.bulk_actions.discord.notification.type.options.updated'),
                    ])
                    ->default('added')
                    ->required()
                    ->rules(['required']),
                ]);
    }
}
