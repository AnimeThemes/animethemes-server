import {
    faClosedCaptioning,
    faComment,
    faCompactDisc,
    faEye,
    faNotEqual,
    faStream
} from "@fortawesome/free-solid-svg-icons";
import ButtonPlay from "./buttonPlay";
import IconText from "./iconText";
import {StyledTagList} from "./layout/tagList.styled";
import {StyledText} from "./layout/text.styled";

export default function VideoBadge({ video }) {
    return (
        <ButtonPlay href={video.link}>
            <StyledTagList>
                <IconText title="Resolution">
                    <StyledText small>{ video.resolution }p</StyledText>
                </IconText>

                {!!video.nc && (
                    <IconText icon={faNotEqual} title="No Credits"/>
                )}

                {!!video.subbed && (
                    <IconText icon={faClosedCaptioning} title="With Subtitles"/>
                )}

                {!!video.lyrics && (
                    <IconText icon={faComment} title="With Lyrics"/>
                )}

                {!!video.uncen && (
                    <IconText icon={faEye} title="Uncensored"/>
                )}

                {!!video.source && (
                    <IconText icon={faCompactDisc} title="Source">
                        <StyledText small>{video.source.toUpperCase()}</StyledText>
                    </IconText>
                )}

                { video.overlap !== "None" && (
                    <IconText icon={faStream} title="Overlap">
                        <StyledText small>{video.overlap.toUpperCase()}</StyledText>
                    </IconText>
                ) }
            </StyledTagList>
        </ButtonPlay>
    );
}
