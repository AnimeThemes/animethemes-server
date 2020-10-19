import {fetchAniListArtist} from "api/aniList";
import {useEffect, useState} from "react";

export default function useAniList(artist) {
    const [image, setImage] = useState(null);

    useEffect(() => {
        let isCanceled = false;

        fetchAniListArtist(artist.name)
            .then(aniListArtist => {
                if (!isCanceled) {
                    setImage(aniListArtist.image);
                }
            });

        return () => { isCanceled = true };
    }, [artist]);

    return { image };
}
