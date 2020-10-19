import React from "react";
import useAniListArtist from "hooks/useAniListArtist";
import SearchResultCard from "components/card/searchResult";

export default function ArtistSearchResultCard({ artist }) {
    const { image } = useAniListArtist(artist);

    return (
        <SearchResultCard
            title={artist.name}
            description={`Artist • ${artist.songs.length} songs`}
            image={image}
        />
    );
}
