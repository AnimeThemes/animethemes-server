import React from "react";
import useSWR from "swr";
import {fetchAnime} from "api/animeThemes/anime";
import useAniList from "hooks/useAniList";
import SearchResultCard from "components/card/searchResult";
import SongTitleWithArtists from "components/utils/songTitleWithArtists";

export default function ThemeSearchResultCard({ theme }) {
    const { data: anime } = useSWR(theme.anime.alias, fetchAnime);
    const { image } = useAniList(anime);

    return (
        <SearchResultCard
            title={<SongTitleWithArtists song={theme.song}/>}
            description={`Theme • ${theme.slug} • ${theme.anime.name}`}
            image={image}
        />
    );
}
