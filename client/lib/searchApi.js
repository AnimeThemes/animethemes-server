const baseUrl = "https://animethemes.dev"

export function fetchSearch(query = "", limit = 3, fields = [ "anime", "themes", "artists" ]) {
    return fetch(`${baseUrl}/api/search?limit=${limit}&fields=${fields.join()}&q=${query}`)
        .then((response) => response.json());
}
