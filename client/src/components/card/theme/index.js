import React from "react";
import {faBomb, faExclamationTriangle, faFilm} from "@fortawesome/free-solid-svg-icons";
import {
    StyledHeader,
    StyledRow,
    StyledSequence,
    StyledThemeCard,
    StyledVersion,
    StyledVideoList,
    StyledVideoListContainer
} from "./style";
import Elevator from "components/elevator";
import Flex from "components/flex";
import Text from "components/text";
import Title from "components/text/title";
import Tag from "components/tag";
import VideoButton from "components/button/video";

export default function ThemeCard({ theme }) {
    return (
        <StyledThemeCard>
            <Elevator>
                <StyledRow>
                    <StyledSequence small>{theme.slug}</StyledSequence>
                    <StyledHeader>
                        <Title variant="card">{theme.song.title}</Title>
                        {!!theme.song.artists && !!theme.song.artists.length && (
                            <>
                                <Text small> by </Text>
                                {theme.song.artists.map((artist, index) => (
                                    <Title variant="card" link key={artist.as || artist.name}>
                                        {(artist.as || artist.name) + (index === theme.song.artists.length - 2 ? " & " : index < theme.song.artists.length - 1 ? ", " : "")}
                                    </Title>
                                ))}
                            </>
                        ) }
                    </StyledHeader>
                </StyledRow>
                {theme.entries.map(entry => (
                    <StyledRow key={entry.version || 0}>
                        <StyledSequence small secondary>{!!entry.version && `v${entry.version}`}</StyledSequence>
                        <StyledVersion>
                            <div>
                                <Flex row wrap gapsBoth="0.75rem">
                                    <Tag icon={faFilm}>
                                        {entry.episodes || "—"}
                                    </Tag>
                                    {!!entry.spoiler && (
                                        <Tag icon={faBomb} warning>
                                            SPOILER
                                        </Tag>
                                    )}
                                    {!!entry.nsfw && (
                                        <Tag icon={faExclamationTriangle} warning>
                                            NSFW
                                        </Tag>
                                    )}
                                </Flex>
                            </div>
                            {!!entry.videos && (
                                <StyledVideoListContainer>
                                    <StyledVideoList>
                                        {entry.videos.map((video, index) => (
                                            <VideoButton key={index} video={video}/>
                                        ))}
                                    </StyledVideoList>
                                </StyledVideoListContainer>
                            )}
                        </StyledVersion>
                    </StyledRow>
                ))}
            </Elevator>
        </StyledThemeCard>
    );
}
