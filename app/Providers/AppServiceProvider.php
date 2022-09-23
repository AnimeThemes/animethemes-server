<?php

declare(strict_types=1);

namespace App\Providers;

use App\Constants\Config\AudioConstants;
use App\Constants\Config\DumpConstants;
use App\Constants\Config\FlagConstants;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Config\Repository;
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
        Repository::macro('bool', function (string $key, bool $default = false): bool {
            /** @var Repository $this */
            return filter_var($this->get($key, $default), FILTER_VALIDATE_BOOLEAN);
        });

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

        AboutCommand::add('Dumps', [
            'Disk' => fn () => Config::get(DumpConstants::DISK_QUALIFIED),
        ]);

        AboutCommand::add('Flags', [
            'Allow Audio Streams' => fn () => Config::bool(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED) ? 'true' : 'false',
            'Allow Discord Notifications' => fn () => Config::bool(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED) ? 'true' : 'false',
            'Allow Video Streams' => fn () => Config::bool(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED) ? 'true' : 'false',
            'Allow View Recording' => fn () => Config::bool(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED) ? 'true' : 'false',
            'Allow Dump Downloading' => fn () => Config::bool(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED) ? 'true' : 'false',
            'Allow Script Downloading' => fn () => Config::bool(FlagConstants::ALLOW_SCRIPT_DOWNLOADING_FLAG_QUALIFIED) ? 'true' : 'false',
        ]);

        AboutCommand::add('Images', [
            'Disk' => fn () => Config::get('image.disk'),
        ]);

        AboutCommand::add('Videos', [
            'Default Disk' => fn () => Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED),
            'Disks' => fn () => implode(',', Config::get(VideoConstants::DISKS_QUALIFIED)),
            'Encoder Version' => fn () => Config::get(VideoConstants::ENCODER_VERSION_QUALIFIED),
            'Nginx Redirect' => fn () => Config::get(VideoConstants::NGINX_REDIRECT_QUALIFIED),
            'Script Disk' => fn () => Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED),
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
