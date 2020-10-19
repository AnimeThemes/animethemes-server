const { baseUrl, fetchJsonCached, createFieldParams } = require("./index");

const fields = createFieldParams({
    anime:    [ "id", "name", "alias", "year", "season" ],
    synonym:  [ "text" ],
    theme:    [ "id", "slug", "group" ],
    song:     [ "title" ],
    artist:   [ "name", "as" ],
    entry:    [ "version", "episodes", "nsfw", "spoiler" ],
    video:    [ "link", "resolution", "nc", "subbed", "lyrics", "uncen", "source", "overlap" ],
    series:   [ "name" ],
    resource: [ "link", "type" ]
});

function fetchAnime(slug) {
    return fetchJsonCached(`${baseUrl}/api/anime/${slug}`);
}

async function fetchAnimeList() {
    const animeList = [];

    let nextUrl = `${baseUrl}/api/anime?limit=100&${fields}`;
    while (nextUrl) {
        const page = await fetchJsonCached(nextUrl);

        animeList.push(...page.anime);

        nextUrl = page.links.next;
    }

    return animeList;
}

function fetchAnimeSearch(query = "", limit = 5) {
    return fetchJsonCached(`${baseUrl}/api/anime?limit=${limit}&${fields}&q=${query}`)
        .then((json) => json.anime);
}

function fetchAnimeSlugs() {
    return fetchAnimeList()
        .then((animeList) => animeList.map((anime) => anime.alias));
}

function fetchAnimeByYear(year) {
    return fetchJsonCached(`${baseUrl}/api/year/${year}`);
}

function fetchAvailableYears() {
    return fetchJsonCached(`${baseUrl}/api/year`);
}

module.exports = {
    fetchAnime,
    fetchAnimeList,
    fetchAnimeSearch,
    fetchAnimeSlugs,
    fetchAnimeByYear,
    fetchAvailableYears
};
