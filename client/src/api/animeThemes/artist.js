const { baseUrl, fetchJsonCached } = require("./index");

export function fetchArtistSearch(query = "", limit = 5) {
    return fetchJsonCached(`${baseUrl}/api/artist?limit=${limit}&q=${query}`)
        .then((json) => json.artists);
}
