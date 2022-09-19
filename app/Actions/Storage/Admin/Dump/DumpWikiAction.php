<?php

declare(strict_types=1);

namespace App\Actions\Storage\Admin\Dump;

use App\Concerns\Repositories\Admin\ReconcilesDumpRepositories;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Audio;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Video;
use App\Pivots\AnimeImage;
use App\Pivots\AnimeResource;
use App\Pivots\AnimeSeries;
use App\Pivots\AnimeStudio;
use App\Pivots\AnimeThemeEntryVideo;
use App\Pivots\ArtistImage;
use App\Pivots\ArtistMember;
use App\Pivots\ArtistResource;
use App\Pivots\ArtistSong;
use App\Pivots\StudioImage;
use App\Pivots\StudioResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class DumpWikiAction.
 */
class DumpWikiAction extends DumpAction
{
    use ReconcilesDumpRepositories;

    /**
     * The list of tables to include in the dump.
     *
     * @return array
     */
    protected function allowedTables(): array
    {
        return [
            Anime::TABLE,
            AnimeImage::TABLE,
            AnimeResource::TABLE,
            AnimeSeries::TABLE,
            AnimeStudio::TABLE,
            AnimeSynonym::TABLE,
            AnimeTheme::TABLE,
            AnimeThemeEntry::TABLE,
            AnimeThemeEntryVideo::TABLE,
            Artist::TABLE,
            ArtistImage::TABLE,
            ArtistMember::TABLE,
            ArtistResource::TABLE,
            ArtistSong::TABLE,
            Audio::TABLE,
            ExternalResource::TABLE,
            Image::TABLE,
            Series::TABLE,
            Song::TABLE,
            Studio::TABLE,
            StudioImage::TABLE,
            StudioResource::TABLE,
            Video::TABLE,
        ];
    }

    /**
     * The temporary path for the database dump.
     * Note: The dumper library does not support writing to disk, so we have to write to the local filesystem first.
     * Pattern: "animethemes-db-dump-wiki-{milliseconds from epoch}.sql".
     *
     * @return string
     */
    protected function getDumpFile(): string
    {
        $filesystem = Storage::disk('local');

        return Str::of($filesystem->path(''))
            ->append('animethemes-db-dump-wiki-')
            ->append(intval(Date::now()->valueOf()))
            ->append('.sql')
            ->__toString();
    }
}
