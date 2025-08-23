<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Middleware\Models\RecordView;
use App\Models\Document\Page;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Audio;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Group;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use App\Pivots\Morph\Imageable;
use App\Pivots\Morph\Resourceable;
use App\Pivots\Wiki\AnimeSeries;
use App\Pivots\Wiki\AnimeStudio;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use App\Pivots\Wiki\ArtistMember;
use App\Pivots\Wiki\ArtistSong;
use Database\Seeders\Auth\Permission\PermissionSeeder;
use Database\Seeders\Auth\Role\AdminSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->setupModels();

        DB::prohibitDestructiveCommands(app()->isProduction());

        EnsureFeaturesAreActive::whenInactive(fn (Request $request, array $features) => new Response(status: 403));

        ParallelTesting::setUpTestDatabase(function (string $database, int $token) {
            Artisan::call('db:seed', ['--class' => PermissionSeeder::class]);
            Artisan::call('db:seed', ['--class' => AdminSeeder::class]);
        });

        DB::listen(function (QueryExecuted $query) {
            if (app()->isLocal()) {
                Log::debug($query->sql);
            }
        });
    }

    /**
     * Set up the model configs.
     */
    protected function setupModels(): void
    {
        Model::automaticallyEagerLoadRelationships();

        Model::preventLazyLoading();

        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
            $class = get_class($model);

            Log::error("Attempted to lazy load '$relation' on model '$class'.", [
                'method' => request()->method(),
                'full-url' => request()->fullUrl(),
                'ip' => request()->ip(),
                'headers' => request()->headers->all(),
            ]);
        });

        Model::preventsAccessingMissingAttributes();

        Model::handleMissingAttributeViolationUsing(function (Model $model, string $key) {
            $class = get_class($model);

            Log::error("Attribute '$key' does not exist or was not retrieved for model '$class'", [
                'method' => request()->method(),
                'full-url' => request()->fullUrl(),
                'ip' => request()->ip(),
                'headers' => request()->headers->all(),
            ]);
        });

        Relation::morphMap([
            'page' => Page::class,
            'anime' => Anime::class,
            'animesynonym' => AnimeSynonym::class,
            'animetheme' => AnimeTheme::class,
            'animethemeentry' => AnimeThemeEntry::class,
            'artist' => Artist::class,
            'audio' => Audio::class,
            'image' => Image::class,
            'membership' => Membership::class,
            'performance' => Performance::class,
            'resource' => ExternalResource::class,
            'series' => Series::class,
            'song' => Song::class,
            'studio' => Studio::class,
            'themegroup' => Group::class,
            'video' => Video::class,
            'videoscript' => VideoScript::class,

            // Pivot
            'imageable' => Imageable::class,
            'resourceable' => Resourceable::class,
            'animeseries' => AnimeSeries::class,
            'animestudio' => AnimeStudio::class,
            'animethemeentryvideo' => AnimeThemeEntryVideo::class,
            'artistmember' => ArtistMember::class,
            'artistsong' => ArtistSong::class,
        ]);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RecordView::class);
    }
}
