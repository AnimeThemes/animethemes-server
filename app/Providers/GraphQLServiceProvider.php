<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\GraphQL\Types\ReportableType;
use App\Enums\GraphQL\SortDirection;
use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\GraphQL\Definition\Input\Base\CreateInput;
use App\GraphQL\Definition\Input\Base\UpdateInput;
use App\GraphQL\Definition\Input\Relations\CreateBelongsToInput;
use App\GraphQL\Definition\Input\Relations\CreateBelongsToManyInput;
use App\GraphQL\Definition\Input\Relations\CreateHasManyInput;
use App\GraphQL\Definition\Input\Relations\UpdateBelongsToInput;
use App\GraphQL\Definition\Input\Relations\UpdateBelongsToManyInput;
use App\GraphQL\Definition\Input\Relations\UpdateHasManyInput;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\EnumType;
use App\GraphQL\Definition\Types\Pivot\PivotType;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;
use ReflectionClass;

class GraphQLServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bootEnums();

        //  $this->bootTypes();
    }

    /**
     * Register the enums to use as type.
     */
    protected function bootEnums(): void
    {
        GraphQL::addType(new EnumType(SortDirection::class));
        GraphQL::addType(new EnumType(ExternalEntryWatchStatus::class));
        GraphQL::addType(new EnumType(ExternalProfileSite::class));
        GraphQL::addType(new EnumType(ExternalProfileVisibility::class));
        GraphQL::addType(new EnumType(PlaylistVisibility::class));
        GraphQL::addType(new EnumType(AnimeMediaFormat::class));
        GraphQL::addType(new EnumType(AnimeSeason::class));
        GraphQL::addType(new EnumType(AnimeSynonymType::class));
        GraphQL::addType(new EnumType(ImageFacet::class));
        GraphQL::addType(new EnumType(ResourceSite::class));
        GraphQL::addType(new EnumType(ThemeType::class));
        GraphQL::addType(new EnumType(VideoOverlap::class));
        GraphQL::addType(new EnumType(VideoSource::class));
    }

    /**
     * Register the types that were made programmatically.
     */
    protected function bootTypes(): void
    {
        $dispatcher = app(Dispatcher::class);

        foreach (File::allFiles(app_path('GraphQL/Definition/Types')) as $file) {
            $fullClass = Str::of($file->getPathname())
                ->after(app_path())
                ->prepend('App')
                ->replace(['/', '.php'], ['\\', ''])
                ->toString();

            if ($fullClass === EnumType::class) {
                continue;
            }

            $reflection = new ReflectionClass($fullClass);

            if (! $reflection->isInstantiable()) {
                continue;
            }

            $class = $reflection->newInstance();

            if (! $class instanceof BaseType) {
                continue;
            }

            // Cache::put("lighthouse.types.{$class->getName()}", $fullClass);

            // if ($class instanceof EloquentType && $class instanceof ReportableType) {
            //         $dispatcher->listen(BuildSchemaString::class, fn () => new CreateInput($class)->__toString());
            //         $dispatcher->listen(BuildSchemaString::class, fn () => new UpdateInput($class)->__toString());

            //     $dispatcher->listen(BuildSchemaString::class, fn () => new CreateBelongsToInput($class)->__toString());
            //     $dispatcher->listen(BuildSchemaString::class, fn () => new CreateHasManyInput($class)->__toString());
            //     $dispatcher->listen(BuildSchemaString::class, fn () => new UpdateBelongsToInput($class)->__toString());
            //     $dispatcher->listen(BuildSchemaString::class, fn () => new UpdateHasManyInput($class)->__toString());

            //     if ($class instanceof PivotType) {
            //         $dispatcher->listen(BuildSchemaString::class, fn () => new CreateBelongsToManyInput($class)->__toString());
            //         $dispatcher->listen(BuildSchemaString::class, fn () => new UpdateBelongsToManyInput($class)->__toString());
            //     }
            // }
        }
    }
}
