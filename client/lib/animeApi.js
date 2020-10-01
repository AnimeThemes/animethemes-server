import withCache from "../utils/withCache";

const baseUrl = "https://animethemes.dev";

const fields = [
    "anime.*.id","anime.*.name","anime.*.alias","anime.*.year","anime.*.season","anime.*.synonyms.*.text","anime.*.themes.*.slug","anime.*.themes.*.group","anime.*.themes.*.song.title",
    "anime.*.themes.*.song.artists.*.name","anime.*.themes.*.song.artists.*.as","anime.*.themes.*.id","anime.*.themes.*.entries.*.version","anime.*.themes.*.entries.*.episodes",
    "anime.*.themes.*.entries.*.nsfw","anime.*.themes.*.entries.*.spoiler","anime.*.themes.*.entries.*.videos.*.link","anime.*.themes.*.entries.*.videos.*.resolution",
    "anime.*.themes.*.entries.*.videos.*.nc","anime.*.themes.*.entries.*.videos.*.subbed","anime.*.themes.*.entries.*.videos.*.lyrics",
    "anime.*.themes.*.entries.*.videos.*.uncen","anime.*.themes.*.entries.*.videos.*.source","anime.*.themes.*.entries.*.videos.*.overlap","anime.*.series.*.name",
    "anime.*.resources.*.link","anime.*.resources.*.type"
];

export function fetchAnime(slug) {
    return withCache(
        `${baseUrl}/api/anime/${slug}`,
        (url) => fetch(url).then((response) => response.json())
    );
}

export function fetchAnimeList() {
    return withCache(
        `${baseUrl}/api/anime?limit=10&fields=${fields.join()}`,
        (url) => fetch(url)
            .then((response) => response.json())
            .then((json) => json.anime)
    );
}

export function fetchAnimeSearch(query = "", limit = 5) {
    return withCache(
        `${baseUrl}/api/anime?limit=${limit}&fields=${fields.join()}&q=${query}`,
        (url) => fetch(url)
            .then((response) => response.json())
            .then((json) => json.anime)
    );
}

export function fetchAnimeSlugs() {
    return fetchAnimeList()
        .then((animeList) => animeList.map((anime) => anime.alias));
}

export function fetchAnimeByYear(year) {
    return withCache(
        `${baseUrl}/api/year/${year}`,
        (url) => fetch(url).then((response) => response.json())
    );
}

export function fetchAvailableYears() {
    return withCache(
        `${baseUrl}/api/year`,
        (url) => fetch(url).then((response) => response.json())
    );
}
