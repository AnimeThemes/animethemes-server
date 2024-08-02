<?php

declare(strict_types=1);

namespace Database\Seeders\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use Exception;
use Http\Client\Exception\RequestException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Class ResourceAnimePlanetSeeder.
 */
class ResourceAnimePlanetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     * @throws Exception
     */
    public function run(): void
    {
        /** @var ExternalResource[] $resources */
        $resources = ExternalResource::query()
            ->whereHas(ExternalResource::RELATION_ANIME)
            ->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIME_PLANET->value)
            ->where(ExternalResource::ATTRIBUTE_EXTERNAL_ID, null)
            ->get();

        foreach ($resources as $resource) {
            try {
                $response = Http::withUserAgent('AnimeThemes/1.0 (https://animethemes.moe)')
                    ->get($resource->link)
                    ->throw()
                    ->body();

                $id = Str::match(
                    '/["\']?ENTRY_INFO["\']? *: *{.*id["\']? *: *["\']?(\d+)["\']? *,/s',
                    $response
                );

                if (empty($id)) continue;

                echo "Updating {$resource->link} to external_id {$id}";
                echo "\n";

                $resource->update([
                    ExternalResource::ATTRIBUTE_EXTERNAL_ID => intval($id)
                ]);
            } catch (RequestException $e) {
                echo $e;
                throw $e;
            }
        }
    }
}
