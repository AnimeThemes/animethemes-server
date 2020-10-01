import useSWR from "swr";
import SearchResultCard from "./searchResultCard";
import {fetchAnime} from "../lib/animeApi";
import useAniList from "../hooks/useAniList";

export default function ThemeSearchResultCard({ theme }) {
    const { data: anime } = useSWR(theme.anime.alias, fetchAnime);
    const { image } = useAniList(anime);

    return (
        <SearchResultCard
            title={theme.song.title}
            description={`Theme • ${theme.slug} • ${theme.anime.name}`}
            image={image}
            href="/theme/[id]"
            as={`/theme/${theme.id}`}
        />
    );
}
