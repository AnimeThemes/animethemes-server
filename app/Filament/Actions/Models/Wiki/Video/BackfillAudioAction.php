<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Video;

use App\Actions\Models\Wiki\Video\Audio\BackfillAudioAction as BackfillAudio;
use App\Enums\Actions\Models\Wiki\Video\DeriveSourceVideo;
use App\Enums\Actions\Models\Wiki\Video\OverwriteAudio;
use App\Enums\Actions\Models\Wiki\Video\ReplaceRelatedAudio;
use App\Filament\Actions\Base\MarkAsReadAction;
use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\Select;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use Exception;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BackfillAudioAction extends BaseAction implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * The default name of the action.
     */
    public static function getDefaultName(): ?string
    {
        return 'backfill-audio';
    }

    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video.backfill.name'));

        $this->icon(__('filament-icons.actions.video.backfill'));

        $this->visible(Gate::allows('create', Audio::class));

        $this->action(fn (Video $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given models.
     *
     * @param  Video  $video
     * @param  array<string, mixed>  $data
     */
    public function handle(Video $video, array $data): void
    {
        $deriveSourceVideo = Arr::get($data, DeriveSourceVideo::getFieldKey());
        $overwriteAudio = Arr::get($data, OverwriteAudio::getFieldKey());
        $replaceRelatedAudio = Arr::get($data, ReplaceRelatedAudio::getFieldKey());

        $action = new BackfillAudio($video, $deriveSourceVideo, $overwriteAudio, $replaceRelatedAudio);

        try {
            $result = $action->handle();
            if ($result->hasFailed()) {
                Notification::make()
                    ->body($result->getMessage())
                    ->warning()
                    ->actions([
                        MarkAsReadAction::make(),
                    ])
                    ->sendToDatabase(Auth::user());
            }
        } catch (Exception $e) {
            $this->failedLog($e);
        }
    }

    /**
     * Get the schema available on the action.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getSchema(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make(DeriveSourceVideo::getFieldKey())
                    ->label(__('filament.actions.video.backfill.fields.derive_source.name'))
                    ->helperText(__('filament.actions.video.backfill.fields.derive_source.help'))
                    ->options(DeriveSourceVideo::class)
                    ->required()
                    ->default(DeriveSourceVideo::YES),

                Select::make(OverwriteAudio::getFieldKey())
                    ->label(__('filament.actions.video.backfill.fields.overwrite.name'))
                    ->helperText(__('filament.actions.video.backfill.fields.overwrite.help'))
                    ->options(OverwriteAudio::class)
                    ->required()
                    ->default(OverwriteAudio::NO),

                Select::make(ReplaceRelatedAudio::getFieldKey())
                    ->label(__('filament.actions.video.backfill.fields.replace_related.name'))
                    ->helperText(__('filament.actions.video.backfill.fields.replace_related.help'))
                    ->options(ReplaceRelatedAudio::class)
                    ->required()
                    ->default(ReplaceRelatedAudio::NO),
            ]);
    }
}
