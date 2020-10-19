import React from "react";
import useSWR from "swr";
import {fetchAnime} from "api/animeThemes/anime";
import useAniList from "hooks/useAniList";
import SearchResultCard from "components/card/searchResult";

export default function ThemeSearchResultCard({ theme }) {
    const { data: anime } = useSWR(theme.anime.alias, fetchAnime);
    const { image } = useAniList(anime);

    return (
        <SearchResultCard
            title={theme.song.title}
            description={`Theme • ${theme.slug} • ${theme.anime.name}`}
            image={image}
        />
    );
}
