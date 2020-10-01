import {useRef} from "react";
import useSWR from "swr";
import {fetchAnimeSearch} from "../lib/animeApi";
import {fetchThemeSearch} from "../lib/themeApi";
import {fetchArtistSearch} from "../lib/artistApi";
import {fetchSearch} from "../lib/searchApi";

export default function useSearch(entity, query) {
    const { data: results, isValidating } = useSWR(query ? [ entity, query ] : null, fetchSearchResults);

    const stickyResults = useRef();
    if (results !== undefined) {
        stickyResults.current = results;
    }

    return [ stickyResults.current, isValidating ];
}

async function fetchSearchResults(entity, query) {
    let results = {
        animeResults: [],
        themeResults: [],
        artistResults: []
    };

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

    return results;
}
