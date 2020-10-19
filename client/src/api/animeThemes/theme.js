const { baseUrl, fetchJsonCached } = require("./index");

export function fetchTheme(id) {
    return fetchJsonCached(`${baseUrl}/api/theme/${id}`);
}

export function fetchThemeSearch(query = "", limit = 5) {
    return fetchJsonCached(`${baseUrl}/api/theme?limit=${limit}&q=${query}`)
        .then((json) => json.themes);
}
