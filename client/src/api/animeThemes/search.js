const { baseUrl, fetchJsonCached } = require("./index");

export function fetchSearch(query = "", limit = 3, fields = [ "anime", "themes", "artists" ]) {
    return fetchJsonCached(`${baseUrl}/api/search?limit=${limit}&q=${query}`);
}
