import {fetchQuery, gql} from "api/graphql";

const url = "https://graphql.anilist.co";

export async function fetchAniListResources(animeId) {
    const aniListAnime = await fetchQuery(url, gql`
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
    const aniListArtist = await fetchQuery(url, gql`
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
