<?php

declare(strict_types=1);

namespace App\Providers;

use App\Constants\Config\AudioConstants;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Model::preventLazyLoading();

        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
            $class = get_class($model);

            Log::info("Attempted to lazy load [$relation] on model [$class]");
        });

        AboutCommand::add('Audios', [
            'Default Disk' => fn () => Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED),
            'Disks' => fn () => implode(',', Config::get(AudioConstants::DISKS_QUALIFIED)),
            'Nginx Redirect' => fn () => Config::get(AudioConstants::NGINX_REDIRECT_QUALIFIED),
            'Streaming Method' => fn () => Config::get(AudioConstants::STREAMING_METHOD_QUALIFIED),
        ]);

        AboutCommand::add('Flags', [
            'Allow Audio Streams' => fn () => Config::get('flags.allow_audio_streams') ? 'true' : 'false',
            'Allow Discord Notifications' => fn () => Config::get('flags.allow_discord_notifications') ? 'true' : 'false',
            'Allow Video Streams' => fn () => Config::get('flags.allow_video_streams') ? 'true' : 'false',
            'Allow View Recording' => fn () => Config::get('flags.allow_view_recording') ? 'true' : 'false',
        ]);

        AboutCommand::add('Images', [
            'Disk' => fn () => Config::get('image.disk'),
        ]);

        AboutCommand::add('Videos', [
            'Default Disk' => fn () => Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED),
            'Disks' => fn () => implode(',', Config::get(VideoConstants::DISKS_QUALIFIED)),
            'Encoder Version' => fn () => Config::get(VideoConstants::ENCODER_VERSION_QUALIFIED),
            'Nginx Redirect' => fn () => Config::get(VideoConstants::NGINX_REDIRECT_QUALIFIED),
            'Streaming Method' => fn () => Config::get(VideoConstants::STREAMING_METHOD_QUALIFIED),
        ]);

        AboutCommand::add('Wiki', [
            'Donate' => fn () => Config::get('wiki.donate'),
            'FAQ' => fn () => Config::get('wiki.faq'),
            'Featured Entry' => function () {
                /** @var AnimeThemeEntry|null $entry */
                $entry = AnimeThemeEntry::query()->find(Config::get('wiki.featured_entry'));

                return $entry?->getName();
            },
            'Featured Video' => function () {
                /** @var Video|null $video */
                $video = Video::query()->find(Config::get('wiki.featured_video'));

                return $video?->getName();
            },
        ]);
    }
}
