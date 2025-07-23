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
use App\Models\Wiki\Group;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use App\Models\Wiki\Studio;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use App\Pivots\Wiki\AnimeImage;
use App\Pivots\Wiki\AnimeResource;
use App\Pivots\Wiki\AnimeSeries;
use App\Pivots\Wiki\AnimeStudio;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use App\Pivots\Wiki\ArtistImage;
use App\Pivots\Wiki\ArtistMember;
use App\Pivots\Wiki\ArtistResource;
use App\Pivots\Wiki\ArtistSong;
use App\Pivots\Wiki\SongResource;
use App\Pivots\Wiki\StudioImage;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DumpWikiAction extends DumpAction
{
    use ReconcilesDumpRepositories;

    final public const FILENAME_PREFIX = 'animethemes-db-dump-wiki-';

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
            Group::TABLE,
            Image::TABLE,
            Membership::TABLE,
            Performance::TABLE,
            Series::TABLE,
            Song::TABLE,
            SongResource::TABLE,
            Studio::TABLE,
            StudioImage::TABLE,
            StudioResource::TABLE,
            Video::TABLE,
            VideoScript::TABLE,
        ];
    }

    /**
     * The temporary path for the database dump.
     * Note: The dumper library does not support writing to disk, so we have to write to the local filesystem first.
     * Pattern: "animethemes-db-dump-wiki-{milliseconds from epoch}.sql".
     */
    protected function getDumpFile(): string
    {
        $filesystem = Storage::disk('local');

        return Str::of($filesystem->path(''))
            ->append(DumpWikiAction::FILENAME_PREFIX)
            ->append(strval(Date::now()->valueOf()))
            ->append('.sql')
            ->__toString();
    }
}
