<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Video;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use App\Actions\Storage\Wiki\Video\UploadVideoAction as UploadVideo;
use App\Constants\Config\VideoConstants;
use App\Enums\Actions\Models\Wiki\Video\ShouldBackfillAudio;
use App\Enums\Actions\Models\Wiki\Video\ShouldSendNotification;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Filament\Actions\Models\Wiki\Video\BackfillAudioAction;
use App\Filament\BulkActions\Models\Wiki\Video\VideoDiscordNotificationBulkAction;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\Select;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Auth\User as UserResource;
use App\Filament\Resources\Wiki\Anime\Theme\Entry\RelationManagers\VideoEntryRelationManager;
use App\Filament\Resources\Wiki\Video\Pages\ListVideos;
use App\Filament\Actions\Storage\Base\UploadAction;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Rules\Wiki\Submission\Audio\AudioChannelLayoutStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioChannelsStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioCodecStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioIndexStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioLoudnessIntegratedTargetStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioLoudnessTruePeakStreamRule;
use App\Rules\Wiki\Submission\Audio\AudioSampleRateStreamRule;
use App\Rules\Wiki\Submission\Format\EncoderNameFormatRule;
use App\Rules\Wiki\Submission\Format\EncoderVersionFormatRule;
use App\Rules\Wiki\Submission\Format\ExtraneousChaptersFormatRule;
use App\Rules\Wiki\Submission\Format\ExtraneousMetadataFormatRule;
use App\Rules\Wiki\Submission\Format\FormatNameFormatRule;
use App\Rules\Wiki\Submission\Format\TotalStreamsFormatRule;
use App\Rules\Wiki\Submission\Format\VideoBitrateRestrictionFormatRule;
use App\Rules\Wiki\Submission\Video\VideoCodecStreamRule;
use App\Rules\Wiki\Submission\Video\VideoColorPrimariesStreamRule;
use App\Rules\Wiki\Submission\Video\VideoColorSpaceStreamRule;
use App\Rules\Wiki\Submission\Video\VideoColorTransferStreamRule;
use App\Rules\Wiki\Submission\Video\VideoIndexStreamRule;
use App\Rules\Wiki\Submission\Video\VideoPixelFormatStreamRule;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File as FileRule;

/**
 * Class UploadVideoAction.
 */
class UploadVideoAction extends UploadAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video.upload.name'));

        $this->visible(Auth::user()->can('create', Video::class));
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
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('video')
                            ->label(__('filament.resources.singularLabel.video'))
                            ->schema([
                                Section::make(__('filament.resources.singularLabel.video'))
                                    ->schema([
                                        Hidden::make(AnimeThemeEntry::ATTRIBUTE_ID)
                                            ->label(__('filament.resources.singularLabel.anime_theme_entry'))
                                            ->default(fn (BaseRelationManager|ListVideos $livewire) => $livewire instanceof VideoEntryRelationManager ? $livewire->getOwnerRecord()->getKey() : null),

                                        ...parent::getSchema($schema)->getComponents(),

                                        Checkbox::make(Video::ATTRIBUTE_NC)
                                            ->label(__('filament.fields.video.nc.name'))
                                            ->helperText(__('filament.fields.video.nc.help'))
                                            ->nullable()
                                            ->rules(['boolean']),

                                        Checkbox::make(Video::ATTRIBUTE_SUBBED)
                                            ->label(__('filament.fields.video.subbed.name'))
                                            ->helperText(__('filament.fields.video.subbed.help'))
                                            ->nullable()
                                            ->rules(['boolean']),

                                        Checkbox::make(Video::ATTRIBUTE_LYRICS)
                                            ->label(__('filament.fields.video.lyrics.name'))
                                            ->helperText(__('filament.fields.video.lyrics.help'))
                                            ->nullable()
                                            ->rules(['boolean']),

                                        Checkbox::make(Video::ATTRIBUTE_UNCEN)
                                            ->label(__('filament.fields.video.uncen.name'))
                                            ->helperText(__('filament.fields.video.uncen.help'))
                                            ->nullable()
                                            ->rules(['boolean']),

                                        Select::make(Video::ATTRIBUTE_OVERLAP)
                                            ->label(__('filament.fields.video.overlap.name'))
                                            ->helperText(__('filament.fields.video.overlap.help'))
                                            ->options(VideoOverlap::asSelectArray())
                                            ->required()
                                            ->enum(VideoOverlap::class),

                                        Select::make(Video::ATTRIBUTE_SOURCE)
                                            ->label(__('filament.fields.video.source.name'))
                                            ->helperText(__('filament.fields.video.source.help'))
                                            ->options(VideoSource::asSelectArray())
                                            ->required()
                                            ->enum(VideoSource::class),
                                    ]),

                                Section::make(__('filament.resources.singularLabel.video_script'))
                                    ->schema([
                                        FileUpload::make('script')
                                            ->label(__('filament.resources.singularLabel.video_script'))
                                            ->helperText(__('filament.actions.storage.upload.fields.file.help'))
                                            ->rule(FileRule::types('txt')->max(2 * 1024))
                                            ->storeFiles(false),

                                        BelongsTo::make('encoder')
                                            ->resource(UserResource::class)
                                            ->label(__('filament.actions.storage.upload.fields.encoder.name'))
                                            ->helperText(__('filament.actions.storage.upload.fields.encoder.help'))
                                            ->withSubtitle(false)
                                            ->default(Auth::id()),
                                    ]),
                            ]),

                        Tab::make('audio')
                            ->label(__('filament.actions.video.backfill.name'))
                            ->schema([
                                Select::make(ShouldBackfillAudio::getFieldKey())
                                    ->label(__('filament.actions.video.backfill.fields.should.name'))
                                    ->helperText(__('filament.actions.video.backfill.fields.should.help'))
                                    ->options(ShouldBackfillAudio::asSelectArray())
                                    ->required()
                                    ->enum(ShouldBackfillAudio::class)
                                    ->default(ShouldBackfillAudio::YES->value),

                                ...BackfillAudioAction::make()->getSchema($schema)->getComponents(),
                            ]),

                        Tab::make('discord')
                            ->label(__('filament.bulk_actions.discord.notification.name'))
                            ->schema([
                                Select::make(ShouldSendNotification::getFieldKey())
                                    ->label(__('filament.bulk_actions.discord.notification.should_send.name'))
                                    ->helperText(__('filament.bulk_actions.discord.notification.should_send.help'))
                                    ->options(ShouldSendNotification::asSelectArray())
                                    ->required()
                                    ->enum(ShouldSendNotification::class)
                                    ->default(ShouldSendNotification::YES->value),

                                ...VideoDiscordNotificationBulkAction::make()->getSchema($schema)->getComponents(),
                            ]),
                    ])
            ]);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Model|null  $model
     * @param  array  $fields
     * @return UploadVideo
     */
    protected function storageAction(?Model $model, array $fields): UploadVideo
    {
        /** @var string|null $path */
        $path = Arr::get($fields, 'path');

        /** @var UploadedFile $file */
        $file = Arr::get($fields, 'file');

        /** @var AnimeThemeEntry|null $entry */
        $entry = AnimeThemeEntry::query()->find(Arr::get($fields, AnimeThemeEntry::ATTRIBUTE_ID));

        /** @var UploadedFile|null $script */
        $script = Arr::get($fields, 'script');

        /** @var User|null $encoder */
        $encoder = User::query()->find(Arr::get($fields, 'encoder'));

        if ($path === null && $entry !== null) {
            $anime = $entry->animetheme->anime;
            $year = $anime->year;
            $path = $year >= 2000
                ? Str::of(strval($year))
                    ->append('/')
                    ->append($anime->season->localize())
                    ->__toString()
                : floor($year % 100 / 10) . '0s';
        }

        if ($path === null) {
            $video = Video::query()->firstWhere(Video::ATTRIBUTE_BASENAME, $file->getClientOriginalName());
            $path = $video instanceof Video ? Str::beforeLast($video->path(), '/') : '';
        }

        $attributes = [
            Video::ATTRIBUTE_NC => Arr::get($fields, Video::ATTRIBUTE_NC),
            Video::ATTRIBUTE_SUBBED => Arr::get($fields, Video::ATTRIBUTE_SUBBED),
            Video::ATTRIBUTE_LYRICS => Arr::get($fields, Video::ATTRIBUTE_LYRICS),
            Video::ATTRIBUTE_UNCEN => Arr::get($fields, Video::ATTRIBUTE_UNCEN),
            Video::ATTRIBUTE_OVERLAP => Arr::get($fields, Video::ATTRIBUTE_OVERLAP),
            Video::ATTRIBUTE_SOURCE => Arr::get($fields, Video::ATTRIBUTE_SOURCE),
        ];

        return new UploadVideo($file, $path, $attributes, $entry, $script, $encoder);
    }

    /**
     * Run this after the video is uploaded.
     *
     * @param  Video  $video
     * @param  array  $data
     * @return void
     */
    protected function afterUploaded(?Model $video, array $data): void
    {
        $shouldBackfill = ShouldBackfillAudio::from(intval(Arr::get($data, ShouldBackfillAudio::getFieldKey())));
        $shouldSendNot = ShouldSendNotification::from(intval(Arr::get($data, ShouldSendNotification::getFieldKey())));

        if ($shouldBackfill === ShouldBackfillAudio::YES) {
            $backfillAudioAction = new BackfillAudioAction('audio');
            $backfillAudioAction->handle($video, $data);
        }

        if ($shouldSendNot === ShouldSendNotification::YES) {
            $videos = new Collection([$video]);

            $discordNotificationAction = new VideoDiscordNotificationBulkAction('discord');
            $discordNotificationAction->handle($videos, $data);
        }
    }

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Get the file validation rules.
     *
     * @return array
     */
    protected function fileRules(): array
    {
        return [
            'required',
            FileRule::types('webm')->max(200 * 1024),
            new TotalStreamsFormatRule(2),
            new EncoderNameFormatRule(),
            new EncoderVersionFormatRule(),
            new FormatNameFormatRule('matroska,webm'),
            new VideoBitrateRestrictionFormatRule(),
            new ExtraneousMetadataFormatRule(),
            new ExtraneousChaptersFormatRule(),
            new AudioIndexStreamRule(1),
            new AudioCodecStreamRule(),
            new AudioSampleRateStreamRule(),
            new AudioChannelsStreamRule(),
            new AudioChannelLayoutStreamRule(),
            new AudioLoudnessTruePeakStreamRule(),
            new AudioLoudnessIntegratedTargetStreamRule(),
            new VideoIndexStreamRule(),
            new VideoCodecStreamRule(),
            new VideoPixelFormatStreamRule(),
            new VideoColorSpaceStreamRule(),
            new VideoColorTransferStreamRule(),
            new VideoColorPrimariesStreamRule(),
        ];
    }
}
