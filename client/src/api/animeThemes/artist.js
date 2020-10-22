const { baseUrl, fetchJsonCached } = require("./index");

export function fetchArtistSearch(query = "", limit = 5) {
    return fetchJsonCached(`${baseUrl}/api/artist?page[size]=${limit}&q=${query}`)
        .then((json) => json.artists);
}
