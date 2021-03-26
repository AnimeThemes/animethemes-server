<?php

namespace Database\Seeders;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WikiPages
{
    const ANIME_INDEX = 'https://www.reddit.com/r/AnimeThemes/wiki/anime_index.json';

    const ARTIST_INDEX = 'https://www.reddit.com/r/AnimeThemes/wiki/artist.json';

    const SERIES_INDEX = 'https://www.reddit.com/r/AnimeThemes/wiki/series.json';

    const MISC_INDEX = 'https://www.reddit.com/r/AnimeThemes/wiki/misc.json';

    const YEAR_MAP = [
        'https://www.reddit.com/r/AnimeThemes/wiki/60s.json' => [1960, 1961, 1962, 1963, 1964, 1965, 1966, 1967, 1968, 1969],
        'https://www.reddit.com/r/AnimeThemes/wiki/70s.json' => [1970, 1971, 1972, 1973, 1974, 1975, 1976, 1977, 1978, 1979],
        'https://www.reddit.com/r/AnimeThemes/wiki/80s.json' => [1980, 1981, 1982, 1983, 1984, 1985, 1986, 1987, 1988, 1989],
        'https://www.reddit.com/r/AnimeThemes/wiki/90s.json' => [1990, 1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998, 1999],
        'https://www.reddit.com/r/AnimeThemes/wiki/2000.json' => [2000],
        'https://www.reddit.com/r/AnimeThemes/wiki/2001.json' => [2001],
        'https://www.reddit.com/r/AnimeThemes/wiki/2002.json' => [2002],
        'https://www.reddit.com/r/AnimeThemes/wiki/2003.json' => [2003],
        'https://www.reddit.com/r/AnimeThemes/wiki/2004.json' => [2004],
        'https://www.reddit.com/r/AnimeThemes/wiki/2005.json' => [2005],
        'https://www.reddit.com/r/AnimeThemes/wiki/2006.json' => [2006],
        'https://www.reddit.com/r/AnimeThemes/wiki/2007.json' => [2007],
        'https://www.reddit.com/r/AnimeThemes/wiki/2008.json' => [2008],
        'https://www.reddit.com/r/AnimeThemes/wiki/2009.json' => [2009],
        'https://www.reddit.com/r/AnimeThemes/wiki/2010.json' => [2010],
        'https://www.reddit.com/r/AnimeThemes/wiki/2011.json' => [2011],
        'https://www.reddit.com/r/AnimeThemes/wiki/2012.json' => [2012],
        'https://www.reddit.com/r/AnimeThemes/wiki/2013.json' => [2013],
        'https://www.reddit.com/r/AnimeThemes/wiki/2014.json' => [2014],
        'https://www.reddit.com/r/AnimeThemes/wiki/2015.json' => [2015],
        'https://www.reddit.com/r/AnimeThemes/wiki/2016.json' => [2016],
        'https://www.reddit.com/r/AnimeThemes/wiki/2017.json' => [2017],
        'https://www.reddit.com/r/AnimeThemes/wiki/2018.json' => [2018],
        'https://www.reddit.com/r/AnimeThemes/wiki/2019.json' => [2019],
        'https://www.reddit.com/r/AnimeThemes/wiki/2020.json' => [2020],
        'https://www.reddit.com/r/AnimeThemes/wiki/2021.json' => [2021],
    ];

    /**
     * Get list of years that correspond to Anime Index Year Notation.
     *
     * @param string $year
     * @return array
     */
    public static function getAnimeIndexYears(string $year)
    {
        foreach (self::YEAR_MAP as $page => $years) {
            if (Str::contains($page, $year)) {
                return $years;
            }
        }

        return [];
    }

    /**
     * Get address of artist page.
     *
     * @param string $slug
     * @return string
     */
    public static function getArtistPage(string $slug)
    {
        return Str::of('https://www.reddit.com/r/AnimeThemes/wiki/artist/')
            ->append($slug)
            ->append('.json')
            ->__toString();
    }

    /**
     * Get address of series page.
     *
     * @param string $slug
     * @return string
     */
    public static function getSeriesPage(string $slug)
    {
        return Str::of('https://www.reddit.com/r/AnimeThemes/wiki/series/')
            ->append($slug)
            ->append('.json')
            ->__toString();
    }

    /**
     * Get contents of reddit wiki page.
     *
     * @param string $page
     * @return mixed
     */
    public static function getPageContents(string $page)
    {
        try {
            $client = new Client;

            $response = $client->get($page);

            $contents = json_decode($response->getBody()->getContents());

            return $contents->data->content_md;
        } catch (ClientException $e) {
            // We may be requesting an invalid Reddit page
            Log::info($e->getMessage());
        } catch (ServerException $e) {
            // We may have upset Reddit
            Log::info($e->getMessage());
            abort(500);
        }
    }
}
