const baseUrl = "https://animethemes.dev"

const fields = [
    "anime.*.name","anime.*.alias","anime.*.year","anime.*.season","anime.*.synonyms.*.text","anime.*.themes.*.slug","anime.*.themes.*.group","anime.*.themes.*.song.title",
    "anime.*.themes.*.song.artists.*.name","anime.*.themes.*.song.artists.*.as","anime.*.themes.*.entries.*.version","anime.*.themes.*.entries.*.episodes",
    "anime.*.themes.*.entries.*.nsfw","anime.*.themes.*.entries.*.spoiler","anime.*.themes.*.entries.*.videos.*.link","anime.*.themes.*.entries.*.videos.*.resolution",
    "anime.*.themes.*.entries.*.videos.*.nc","anime.*.themes.*.entries.*.videos.*.subbed","anime.*.themes.*.entries.*.videos.*.lyrics",
    "anime.*.themes.*.entries.*.videos.*.uncen","anime.*.themes.*.entries.*.videos.*.source","anime.*.themes.*.entries.*.videos.*.overlap","anime.*.series.*.name",
    "anime.*.resources.*.link","anime.*.resources.*.type"
];

export function fetchTheme(id) {
    return fetch(`${baseUrl}/api/theme/${id}`)
        .then((response) => response.json());
}

export function fetchThemeSearch(query = "", limit = 5) {
    return fetch(`${baseUrl}/api/theme?limit=${limit}&q=${query}`)
        .then((response) => response.json())
        .then((json) => json.themes);
}
