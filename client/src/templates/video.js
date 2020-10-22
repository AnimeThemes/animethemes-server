import {Link} from "gatsby";
import Flex, {FlexItem} from "components/flex";
import VideoPlayer from "components/videoPlayer";
import styled from "styled-components";
import Text from "components/text";
import useAniList from "hooks/useAniList";
import SongTitleWithArtists from "components/utils/songTitleWithArtists";
import VideoTags from "components/utils/videoTags";
import ThemeEntryTags from "components/utils/themeEntryTags";
import Button from "components/button";
import {useEffect} from "react";

const StyledCover = styled.img`
    width: 48px;
    height: 64px;
    object-fit: cover;
`;
const StyledVideoInfo = styled(Flex).attrs({
    row: true,
    alignItems: "center",
    gapsRow: "1rem"
})`
    padding: 0 1rem;
`;

export default function VideoPage({ pageContext: { video, anime, theme, entry } }) {
    const { image } = useAniList(anime);

    useEffect(() => {
        if (theme && image) {
            // eslint-disable-next-line no-undef
            navigator.mediaSession.metadata = new MediaMetadata({
                title: `${theme.slug} • ${theme.song.title}`,
                artist: theme.song.artists.map((artist) => artist.as || artist.name).join(", "),
                album: anime.name,
                artwork: [
                    { src: image, sizes: "512x512", type: "image/jpeg" }
                ]
            });
        }
    }, [ theme, image ]);

    return (
        <Flex gapsColumn="1rem">
            <VideoPlayer src={video.link.replace(".dev", ".moe")}/>
            <StyledVideoInfo>
                <StyledCover alt="Cover" src={image}/>
                <FlexItem flex={1}>
                    <Flex justifyContent="center" gapsColumn="0.25rem">
                        <SongTitleWithArtists song={theme.song}/>
                        <Text small maxLines={1}>
                            <Text>{theme.slug} from </Text>
                            <Link to={`/anime/${anime.alias}`}>
                                <Text link>{anime.name}</Text>
                            </Link>
                        </Text>
                    </Flex>
                </FlexItem>
                <Button silent>
                    <Flex row alignItems="center" gapsRow="0.5rem">
                        <Text small>Version {entry.version || 1}</Text>
                        <ThemeEntryTags entry={entry}/>
                        <Text link>&bull;</Text>
                        <VideoTags video={video}/>
                    </Flex>
                </Button>
            </StyledVideoInfo>
        </Flex>
    );
}
