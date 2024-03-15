<?php

declare(strict_types=1);

namespace Database\Seeders\Wiki\Anime;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class FixExternalResourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            $availableSites = $this->getAvailableSites();
            $resourceSites = array_values($availableSites);

            $externalResources = ExternalResource::query();

            foreach ($resourceSites as $resourceSite) {
                $externalResources = $externalResources->orWhere(ExternalResource::ATTRIBUTE_SITE, $resourceSite->value);
            }

            $externalResources = $externalResources->get();

            foreach ($externalResources as $externalResource) {
                $url = Arr::get($externalResource, ExternalResource::ATTRIBUTE_LINK);
                $site = Arr::get($externalResource, ExternalResource::ATTRIBUTE_SITE);
                $urlPattern = $site->getUrlPattern();

                if (preg_match($urlPattern, $url, $matches)) {
                    $url = $site->formatAnimeResourceLink(intval($matches[2]), $matches[2], $matches[1]);

                    $externalResource->update([
                        ExternalResource::ATTRIBUTE_LINK => $url,
                    ]);
                }
            }

            DB::commit();

        } catch (Exception $e) {
            echo 'error ' . $e->getMessage();
            echo "\n";

            DB::rollBack();

            throw $e;
        }
    }

    protected function getAvailableSites(): array
    {
        /**  Key name in Anilist API => @var ResourceSite */
        return [
            'Netflix' => ResourceSite::NETFLIX,
            'Crunchyroll' => ResourceSite::CRUNCHYROLL,
            'HIDIVE' => ResourceSite::HIDIVE,
            'Hulu' => ResourceSite::HULU,
            'Disney Plus' => ResourceSite::DISNEY_PLUS,
        ];
    }
}
