import {fetchAniListResources} from "../lib/aniListApi";
import {useMemo} from "react";
import useSWR from "swr";

export default function useAniList(anime) {
    const myAnimeListId = useMemo(
        () => anime && anime.resources && anime.resources.length ? anime.resources[0].link.match(/\d+/)[0] : null,
        [ anime ]
    );

    const { data } = useSWR(
        myAnimeListId,
        fetchAniListResources,
        {
            revalidateOnFocus : false
        }
    );

    return data || { synopsis: "Loading", image: null };
}
