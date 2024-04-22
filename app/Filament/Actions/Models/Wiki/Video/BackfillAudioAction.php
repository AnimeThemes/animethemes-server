<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Video;

use App\Actions\Models\Wiki\Video\Audio\BackfillAudioAction as BackfillAudio;
use App\Enums\Actions\Models\Wiki\Video\DeriveSourceVideo;
use App\Enums\Actions\Models\Wiki\Video\OverwriteAudio;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Enum;

/**
 * Class BackfillAudioAction.
 */
class BackfillAudioAction extends Action implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    final public const DERIVE_SOURCE_VIDEO = 'derive_source_video';
    final public const OVERWRITE_AUDIO = 'overwrite_audio';

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn (Model $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given models.
     *
     * @param  Model  $video
     * @param  array  $data
     * @return void
     */
    public function handle(Model $video, array $data): void
    {
        $deriveSourceVideo = DeriveSourceVideo::from(intval(Arr::get($data, self::DERIVE_SOURCE_VIDEO)));
        $overwriteAudio = OverwriteAudio::from(intval(Arr::get($data, self::OVERWRITE_AUDIO)));

        $action = new BackfillAudio($video, $deriveSourceVideo, $overwriteAudio);

        try {
            $result = $action->handle();
            if ($result->hasFailed()) {
                Notification::make()
                    ->body($result->getMessage())
                    ->warning()
                    ->actions([
                        NotificationAction::make('mark-as-read')
                            ->button()
                            ->markAsRead(),
                    ])
                    ->sendToDatabase(auth()->user());
            }
        } catch (Exception $e) {
            $this->markAsFailed($video, $e);
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getForm(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(self::DERIVE_SOURCE_VIDEO)
                    ->label(__('filament.actions.video.backfill.fields.derive_source.name'))
                    ->options(DeriveSourceVideo::asSelectArray())
                    ->rules(['required', new Enum(DeriveSourceVideo::class)])
                    ->default(DeriveSourceVideo::YES)
                    ->helperText(__('filament.actions.video.backfill.fields.derive_source.help')),

                Select::make(self::OVERWRITE_AUDIO)
                    ->label(__('filament.actions.video.backfill.fields.overwrite.name'))
                    ->options(OverwriteAudio::asSelectArray())
                    ->rules(['required', new Enum(OverwriteAudio::class)])
                    ->default(OverwriteAudio::NO)
                    ->helperText(__('filament.actions.video.backfill.fields.overwrite.help')),
            ]);
    }
}