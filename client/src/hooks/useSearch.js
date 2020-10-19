import {useRef} from "react";
import useSWR from "swr";
import {fetchAnimeSearch} from "api/animeThemes/anime";
import {fetchThemeSearch} from "api/animeThemes/theme";
import {fetchArtistSearch} from "api/animeThemes/artist";
import {fetchSearch} from "api/animeThemes/search";

const createEmptyResults = () => ({
    animeResults: [],
    themeResults: [],
    artistResults: []
});

export default function useSearch(entity, query) {
    const { data: results, isValidating } = useSWR([ entity, query ], fetchSearchResults);

    const stickyResults = useRef();
    if (results !== undefined) {
        stickyResults.current = results;
    }

    return [ stickyResults.current || createEmptyResults(), isValidating ];
}

async function fetchSearchResults(entity, query) {
    let results = createEmptyResults();

    if (query) {
        switch (entity) {
            case "anime":
                results.animeResults = await fetchAnimeSearch(query, 10);
                break;
            case "theme":
                results.themeResults = await fetchThemeSearch(query, 10);
                break;
            case "artist":
                results.artistResults = await fetchArtistSearch(query, 10)
                break;
            default:
                const {anime, themes, artists} = await fetchSearch(query);
                results = {
                    animeResults: anime,
                    themeResults: themes,
                    artistResults: artists
                };
                break;
        }
    }

    return results;
}
