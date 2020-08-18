import {fetchQuery, graphql} from "./graphql";

export async function fetchAniListResources(animeId) {
    const aniListAnime = await fetchQuery(graphql`
        query($id: Int) {
            Media(idMal: $id, type: ANIME) {
                description
                coverImage {
                    extraLarge
                }
            }
        }
    `, {
        id: animeId
    });

    return {
        synopsis: aniListAnime.Media.description,
        image: aniListAnime.Media.coverImage.extraLarge
    };
}
