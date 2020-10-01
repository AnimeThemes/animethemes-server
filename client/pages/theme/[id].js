import styled from "styled-components";
import {fetchTheme} from "../../lib/themeApi";
import {gapsColumn} from "../../styles/utils/gaps";
import VideoPlayer from "../../components/videoPlayer";
import {useRouter} from "next/router";
import {useEffect} from "react";
import {fetchAnime} from "../../lib/animeApi";
import useAniList from "../../hooks/useAniList";
import {StyledText} from "../../components/layout/text.styled";

const StyledThemePage = styled.div`
    ${gapsColumn("1.5rem")}
`;

export default function ThemeDetailPage({ theme, anime }) {
    const router = useRouter();

    if (router.isFallback) {
        return <StyledText>Loading...</StyledText>;
    }

    const { image } = useAniList(anime);

    const videoUrl = theme.entries[0].videos[0].link.replace(".dev", ".moe");

    useEffect(() => {
        if (theme && image) {
            navigator.mediaSession.metadata = new MediaMetadata({
                title: `${theme.slug} • ${theme.song.title}`,
                artist: theme.song.artists.map((artist) => artist.as || artist.name).join(", "),
                album: theme.anime.name,
                artwork: [
                    { src: image, sizes: "512x512", type: "image/jpeg" }
                ]
            });
        }
    }, [theme, image]);

    return (
        <StyledThemePage>
            <VideoPlayer src={videoUrl} controls/>
        </StyledThemePage>
    );
}

export async function getStaticPaths() {
    return {
        paths: [],
        fallback: false,
    };
}

export async function getStaticProps({ params }) {
    const theme = await fetchTheme(params.id);
    const anime = await fetchAnime(theme.anime.alias);

    return {
        props: {
            theme,
            anime
        },
    };
}
