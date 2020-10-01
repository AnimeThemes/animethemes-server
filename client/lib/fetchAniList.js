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

    if (!aniListAnime || !aniListAnime.Media) {
        return {
            synopsis: "",
            image: ""
        }
    }

    return {
        synopsis: aniListAnime.Media.description,
        image: aniListAnime.Media.coverImage.extraLarge
    };
}

export async function fetchAniListArtist(artistQuery) {
    const aniListArtist = await fetchQuery(graphql`
        query($query: String) {
            Staff(search: $query) {
                image {
                    large
                }
            }
        }
    `, {
        query: artistQuery
    });

    if (!aniListArtist || !aniListArtist.Staff) {
        return {
            image: ""
        }
    }

    return {
        image: aniListArtist.Staff.image.large
    };
}
