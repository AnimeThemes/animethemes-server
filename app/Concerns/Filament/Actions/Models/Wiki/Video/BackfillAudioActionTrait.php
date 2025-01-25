<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Models\Wiki\Video;

use App\Actions\Models\Wiki\Video\Audio\BackfillAudioAction as BackfillAudio;
use App\Enums\Actions\Models\Wiki\Video\DeriveSourceVideo;
use App\Enums\Actions\Models\Wiki\Video\OverwriteAudio;
use App\Enums\Actions\Models\Wiki\Video\ReplaceRelatedAudio;
use App\Filament\Components\Fields\Select;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use Exception;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

/**
 * Trait BackfillAudioActionTrait.
 */
trait BackfillAudioActionTrait
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video.backfill.name'));

        $this->icon(__('filament-icons.actions.video.backfill'));

        $this->authorize('create', Audio::class);

        $this->action(fn (Video $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given models.
     *
     * @param  Video  $video
     * @param  array  $data
     * @return void
     */
    public function handle(Video $video, array $data): void
    {
        $deriveSourceVideo = DeriveSourceVideo::from(intval(Arr::get($data, DeriveSourceVideo::getFieldKey())));
        $overwriteAudio = OverwriteAudio::from(intval(Arr::get($data, OverwriteAudio::getFieldKey())));
        $replaceRelatedAudio = ReplaceRelatedAudio::from(intval(Arr::get($data, ReplaceRelatedAudio::getFieldKey())));

        $action = new BackfillAudio($video, $deriveSourceVideo, $overwriteAudio, $replaceRelatedAudio);

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
                    ->sendToDatabase(Auth::user());
            }
        } catch (Exception $e) {
            $this->failedLog($e);
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
                Select::make(DeriveSourceVideo::getFieldKey())
                    ->label(__('filament.actions.video.backfill.fields.derive_source.name'))
                    ->helperText(__('filament.actions.video.backfill.fields.derive_source.help'))
                    ->options(DeriveSourceVideo::asSelectArray())
                    ->rules(['required', new Enum(DeriveSourceVideo::class)])
                    ->default(DeriveSourceVideo::YES->value),

                Select::make(OverwriteAudio::getFieldKey())
                    ->label(__('filament.actions.video.backfill.fields.overwrite.name'))
                    ->helperText(__('filament.actions.video.backfill.fields.overwrite.help'))
                    ->options(OverwriteAudio::asSelectArray())
                    ->rules(['required', new Enum(OverwriteAudio::class)])
                    ->default(OverwriteAudio::NO->value),

                Select::make(ReplaceRelatedAudio::getFieldKey())
                    ->label(__('filament.actions.video.backfill.fields.replace_related.name'))
                    ->helperText(__('filament.actions.video.backfill.fields.replace_related.help'))
                    ->options(ReplaceRelatedAudio::asSelectArray())
                    ->rules(['required', new Enum(ReplaceRelatedAudio::class)])
                    ->default(ReplaceRelatedAudio::NO->value),
            ]);
    }
}
