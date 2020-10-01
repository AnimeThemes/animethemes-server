import VideoBadge from "./videoBadge";
import {faBomb, faExclamationTriangle, faFilm} from "@fortawesome/free-solid-svg-icons";
import {
    StyledHeader,
    StyledRow,
    StyledSequence,
    StyledThemeCard,
    StyledVersion,
    StyledVideoList,
    StyledVideoListContainer
} from "./themeCard.styled";
import IconText from "./iconText";
import Elevator from "./elevator";
import {StyledTagList} from "./layout/tagList.styled";
import {StyledText, StyledTitleCard} from "./layout/text.styled";

export default function ThemeCard({ theme }) {
    return (
        <StyledThemeCard>
            <Elevator>
                <StyledRow>
                    <StyledSequence small>{theme.slug}</StyledSequence>
                    <StyledHeader>
                        <StyledTitleCard>{theme.song.title}</StyledTitleCard>
                        {!!theme.song.artists.length && (
                            <>
                                <StyledText small> by </StyledText>
                                {theme.song.artists.map((artist, index) => (
                                    <StyledTitleCard link key={artist.as || artist.name}>
                                        {(artist.as || artist.name) + (index === theme.song.artists.length - 2 ? " & " : index < theme.song.artists.length - 1 ? ", " : "")}
                                    </StyledTitleCard>
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
                                <StyledTagList>
                                    <IconText icon={faFilm}>
                                        <StyledText small>{entry.episodes || "—"}</StyledText>
                                    </IconText>
                                    {!!entry.spoiler && (
                                        <IconText icon={faBomb} warning>
                                            <StyledText small>SPOILER</StyledText>
                                        </IconText>
                                    )}
                                    {!!entry.nsfw && (
                                        <IconText icon={faExclamationTriangle} warning>
                                            <StyledText small>NSFW</StyledText>
                                        </IconText>
                                    )}
                                </StyledTagList>
                            </div>
                            <StyledVideoListContainer>
                                <StyledVideoList>
                                    {entry.videos.map((video, index) => (
                                        <VideoBadge key={index} video={video}/>
                                    ))}
                                </StyledVideoList>
                            </StyledVideoListContainer>
                        </StyledVersion>
                    </StyledRow>
                ))}
            </Elevator>
        </StyledThemeCard>
    );
}
