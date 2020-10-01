import SearchResultCard from "./searchResultCard";
import useAniListArtist from "../hooks/useAniListArtist";

export default function ArtistSearchResultCard({ artist }) {
    const { image } = useAniListArtist(artist);

    return (
        <SearchResultCard
            title={artist.name}
            description={`Artist • ${artist.songs.length} songs`}
            image={image}
            href="/artist/[alias]"
            as={`/artist/${artist.alias}`}
        />
    );
}
