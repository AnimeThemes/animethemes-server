const baseUrl = "https://animethemes.dev"

export function fetchArtistSearch(query = "", limit = 5) {
    return fetch(`${baseUrl}/api/artist?limit=${limit}&q=${query}`)
        .then((response) => response.json())
        .then((json) => json.artists);
}
