<?php

declare(strict_types=1);

namespace App\Pipes\Wiki\Anime;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use App\Pivots\AnimeImage;
use Closure;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class BackfillAnimeImage.
 */
abstract class BackfillAnimeImage extends BackfillAnimePipe
{
    /**
     * Handle an incoming request.
     *
     * @param  User  $user
     * @param  Closure(User): mixed  $next
     * @return mixed
     *
     * @throws RequestException
     */
    public function handle(User $user, Closure $next): mixed
    {
        if ($this->anime->images()->where(Image::ATTRIBUTE_FACET, $this->getFacet()->value)->exists()) {
            Log::info("Anime '{$this->anime->getName()}' already has Image of Facet '{$this->getFacet()->value}'.");

            return $next($user);
        }

        $image = $this->getImage();

        if ($image !== null) {
            $this->attachImageToAnime($image);
        }

        if ($this->anime->images()->where(Image::ATTRIBUTE_FACET, $this->getFacet()->value)->doesntExist()) {
            $this->sendNotification(
                $user,
                "Anime '{$this->anime->getName()}' has no {$this->getFacet()->description} Image after backfilling. Please review."
            );
        }

        return $next($user);
    }

    /**
     * Create Image from response.
     *
     * @param  string  $url
     * @return Image
     */
    protected function createImage(string $url): Image
    {
        $imageResponse = Http::get($url);

        $image = $imageResponse->body();

        $file = File::createWithContent(basename($url), $image);

        $fs = Storage::disk('images');

        $fsFile = $fs->putFile('', $file);

        return Image::factory()->createOne([
            Image::ATTRIBUTE_FACET => $this->getFacet()->value,
            Image::ATTRIBUTE_PATH => $fsFile,
        ]);
    }

    /**
     * Attach Image to Anime.
     *
     * @param  Image  $image
     * @return void
     */
    protected function attachImageToAnime(Image $image): void
    {
        if (AnimeImage::query()
            ->where($this->anime->getKeyName(), $this->anime->getKey())
            ->where($image->getKeyName(), $image->getKey())
            ->doesntExist()
        ) {
            Log::info("Attaching image '{$image->getName()}' to anime '{$this->anime->getName()}'");
            $image->anime()->attach($this->anime);
        }
    }

    /**
     * Get the facet to backfill.
     *
     * @return ImageFacet
     */
    abstract protected function getFacet(): ImageFacet;

    /**
     * Query third-party APIs to find Image.
     *
     * @return Image|null
     *
     * @throws RequestException
     */
    abstract protected function getImage(): ?Image;
}
